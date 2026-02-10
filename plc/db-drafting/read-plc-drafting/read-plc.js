//connexió PLC siemens S7-1200
const net                 = require('net');
const { ModbusTCPClient } = require('jsmodbus');

//crea socket modbus TCP
const socket = new net.Socket();
const client = new ModbusTCPClient(socket);
const host   = '192.168.103.75'; //PLC adreça ip
const port   = 502;              //PLC TCP modbus port

socket.on('connect', async function(){
  console.log(`Connected to ${host}:${port}`);
  try{
    //read holding registers starting from address 0
    const response = await client.readHoldingRegisters(0, 21);
    console.log('Received Data:', response.response.body.values);
    let lectura = response.response.body.values;

    //si la bomba està engegada inserta lectura
    let cabalgen = lectura[8];
    console.log({cabalgen});
    if(cabalgen){
      g_insertar_a_db = true; //flag global per donar permís inserció a base de dades
    }

  }catch(error){
    console.error('Modbus Read Error:', error);
  }finally{
    socket.end(); // Close connection
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

socket.connect({host,port});
