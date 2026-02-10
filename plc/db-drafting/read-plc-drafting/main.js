//connexió PLC siemens S7-1200
const net                 = require('net');
const { ModbusTCPClient } = require('jsmodbus');

//express http server
const cors    = require('cors');
const express = require('express');

//SSL Certificate and Key
const fs      = require('fs');
const https   = require('https');
const path    = require('path');
const options = {
  key:  fs.readFileSync('server.key'),
  cert: fs.readFileSync('server.cert'),
};

//crea socket modbus TCP
const socket = new net.Socket();
const client = new ModbusTCPClient(socket);
const host   = '192.168.103.75'; //PLC adreça ip
const port   = 502;              //PLC TCP modbus port

//variables globals
let g_llista = null; //array global per guardar valors plc
let g_res    = null; //objecte per enviar llista ("res.send(llista)")

socket.on('connect', async function(){
  console.log(`Connected to Siemens S7-1200 at ${host}:${port}`);

  //valors llegits
  g_llista = null;

  try{
    //read holding registers starting from address 0 (Change based on PLC configuration)
    const response = await client.readHoldingRegisters(0, 21);
    console.log('Received Data:', response.response.body.values);
    g_llista = response.response.body.values;
    if(g_res && g_llista){
      g_res.json(g_llista);
      g_res = null;
    }
    if(g_insertar_a_db){
      inserta_lectura(g_llista);
    }
  }catch(error){
    console.error('Modbus Read Error:', error);
  }finally{
    socket.end(); // Close connection
    g_insertar_a_db = false;
  }
});
socket.on('error',err=>{
  console.error('Connection Error:', err);
  socket.destroy();
});
socket.on('close',()=>{
  //ensure cleanup on close
  console.log('Connection closed.');
});

//crea express server
const app = express();
app.use(cors());

//GET /plc
app.get('/plc',async function(req,res){
  try{
    console.log(new Date(),"GET /plc");

    //connecta al PLC
    g_res = res;
    socket.connect({host,port});
  }catch(err){
    res.status(500).send('Error: ' + err.message);
  }
});

//serve certificate "server.cert"
app.get('/server.cert',(req,res)=>{
  const file_path = path.join(__dirname,'server.cert');
  res.sendFile(file_path);
});

//inicia HTTPS server
https.createServer(options,app).listen(50001,async function(){
  console.log(`Servidor corrent a https://localhost:50001`);
});

//inicia interval lectures | seccio base de dades
const sqlite3 = require('sqlite3').verbose(); //llegir base de dades local
async function inserta_lectura(arr){
  let db = new sqlite3.Database("db/lectures.sqlite");
  let sql=`INSERT into lectures(lectura) VALUES ("${arr}")`;
  await db.run(sql,function(err){
    if(err){
      throw new Error(err);
    }else{
      console.log(`[ok][${new Date()} lectura insertada ["${arr}"]`);
    }
  });
  await db.close();
}

let interval_minuts = 5; //comprovar cada 5 min
let interval_ms     = interval_minuts*60*1000;
let g_insertar_a_db = false; //permís per insertar lectura a bbdd
let g_comptador     =  
setInterval(async function(){
  try{
    g_insertar_a_db = false;
    socket.connect({host,port});
  }catch(error){
    console.error('Modbus Read Error:', error);
  }
},interval_ms);

//GET /db
app.get('/db',(req,res)=>{
  let lectures=[];//return value
  let db = new sqlite3.Database("db/lectures.sqlite");
  (function(){
    return new Promise((resolve,reject)=>{
      db.serialize(function(){
        let sql=`SELECT * FROM lectures`;
        db.each(sql,function(err,lec){
          if(err) throw(err.message);
          lectures.push(lec);
        });
        //resolve Promise with a dummy query
        db.run("SELECT 1",function(err){
          resolve();
        });
      });
      db.close();
    });
  })().then(function(){
    res.send(lectures);
  });
});
