const sqlite3 = require('sqlite3').verbose(); //llegir base de dades local

async function inserta_lectura(arr){
  let db = new sqlite3.Database("lectures.sqlite");
  let sql=`INSERT into lectures(lectura) VALUES ("${arr}")`;

  await db.run(sql,function(err){
    if(err) throw new Error(err);
    console.log(`[ok][${new Date()} lectura insertada ["${arr}"]`);
  });
  await db.close();
}

//test
inserta_lectura([1,2,3]);
