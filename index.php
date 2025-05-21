<?php include'auth.php';?>
<!doctype html><html><head>
  <meta charset=utf8>
  <title>ICRA Paret Verda</title>
  <script src="lib/vue.global.prod.js"></script>
  <style>
    table{
      border-collapse:collapse;
      width:100%;
    }
    a[current=true]{
      background:yellow;
    }
    textarea{
      width:98%;
      field-sizing:content;
    }
    button[pestanya_actual]{
      border:1px solid #ccc;
      border-bottom:none;
      border-radius:5px 5px 0 0;
      cursor:pointer;
      padding:0.618em 1em;
      color:#666;
    }
    button[pestanya_actual=true]{
      color:black;
      background:linear-gradient(#f8f8f8,white);
    }
    .warning{
      color:red;
      font-weight:bold;
    }
    input[type=number]{
      text-align:right;
    }
    summary{
      font-weight:bold;
      cursor:pointer;
    }
    summary:hover{
      text-decoration:underline;
    }
  </style>
</head><body>
<h1>icra: manteniment paret verda (en desenvolupament)</h1><hr>

<div id=app>
  <div>
    <button @click="nou()" :disabled="metadata_sistemes==null">nou</button> |
    <button @click="guardar()" :disabled="sistema==null">guardar</button>
  </div><hr>

  <details v-if="metadata_sistemes" open>
    <summary>
      Sistemes guardats ({{metadata_sistemes.length}})
    </summary>
    <table border=1>
      <tr v-for="sis in metadata_sistemes">
        <td>
          <button @click="carregar(sis.id)">CARREGAR</button> |
          <b>{{sis.nom}}</b> |
          <small>{{sis.lloc}}</small> |
          (id:{{sis.id}})
          <p>{{sis.descripcio}}</p>
          <div style="text-align:right">
            <button @click="esborrar(sis.id)">esborrar</button>
          </div>
        </td>
      </tr>
    </table>
  </details>

  <div v-if="sistema"><hr>
    <!--selector pestanyes-->
    <div
      id=selector_pestanyes
      style="
        display:flex;
        flex-wrap:wrap;
        margin:5px 0;
      "
    >
      <div v-for="p in pestanyes">
        <button
          @click="pestanya_actual=p"
          :pestanya_actual="p==pestanya_actual"
        >{{p.replaceAll("_"," ")}}</button>
      </div>
    </div>

    <!--pestanya general-->
    <div
      v-if="pestanya_actual=='inici'"
      style="
        display:grid;
        grid-template-columns:33% 33% 33%;
        grid-gap:2px;
      "
    >
      <div>
        <h2>Sistema</h2>
        <table border=1>
          <tr>
            <th>Nom sistema<td><input v-model.text="sistema.nom">
          </tr>
          <tr>
            <th>Lloc<td><input v-model.text="sistema.lloc" style="width:90%">
          </tr>
          <tr>
            <th>Descripció
            <td>
              <textarea v-model.text="sistema.descripcio"></textarea>
            </td>
          </tr>
          <tr>
            <th>Dimensionament</th>
            <td>
              <div v-for="_,i in sistema.dimensioning">
                <div
                  style="
                    display:flex;
                    justify-content:space-between;
                  "
                >
                  <input v-model="sistema.dimensioning[i]">
                  <button @click="sistema.dimensioning.splice(i,1)">eliminar característica</button>
                </div>
              </div>
              <button @click="sistema.dimensioning.push('característica: valor unitat')">afegir característica</button>
            </td>
          </tr>
          <tr>
            <th>Aparells</th>
            <td>
              <div>
                <div v-for="ap,i in sistema.aparells">
                  <div
                    style="
                      display:flex;
                      justify-content:space-between;
                    "
                  >
                    <div style="font-size:larger">
                      <a href="#" @click="aparell=ap;tasca=null" :current="aparell==ap">{{ap.nom}}</a>
                    </div>
                    <button @click="sistema.aparells.splice(i,1);tasca=null;aparell=null">eliminar aparell</button>
                  </div>
                </div>
              </div>
              <button @click="afegir_aparell(sistema)">afegir aparell</button>
            </td>
          </tr>
        </table>
      </div>

      <div v-if="aparell">
        <h2>Aparell</h2>
        <table border=1>
          <tr>
            <td colspan=2 style="text-align:right">
              <button @click="tasca=null;aparell=null">X</button>
            </td>
          </tr>
          <tr>
            <th>Nom aparell<td><input v-model.text="aparell.nom">
          </tr>
          <tr>
            <th>Dimensionament</th>
            <td>
              <div v-for="token,i in aparell.dimensioning">
                <div
                  style="
                    display:flex;
                    justify-content:space-between;
                  "
                >
                  <input v-model="aparell.dimensioning[i]">
                  <button @click="aparell.dimensioning.splice(i,1)">eliminar característica</button>
                </div>
              </div>
              <button @click="aparell.dimensioning.push('característica: valor unitat')">afegir característica</button>
            </td>
          </tr>
          <tr>
            <th>Tasques</th>
            <td>
              <div>
                <div v-for="ta,i in aparell.tasques">
                  <div style="
                    display:flex;
                    justify-content:space-between;
                  ">
                    <div style="font-size:larger">
                      <a href="#" @click="tasca=ta;" :current="tasca==ta">{{ta.nom}}</a>
                    </div>
                    <button @click="aparell.tasques.splice(i,1)">eliminar tasca</button>
                  </div>
                </div>
              </div>
              <button @click="afegir_tasca(aparell)">afegir tasca</button>
            </td>
          </tr>
        </table>
      </div>

      <div v-if="aparell && tasca">
        <h2>Tasca</h2>
        <table border=1>
          <tr>
            <td colspan=2 style="text-align:right">
              <button @click="tasca=null">X</button>
            </td>
          </tr>
          <tr><th>Nom tasca<td><input v-model.text="tasca.nom"></tr>
          <tr><th>Descripció<td><textarea v-model.text="tasca.descripcio"></textarea></tr>
          <tr><th>Responsable<td><input v-model.text="tasca.responsable"></tr>
          <tr>
            <th>Material necessari</th>
            <td>
              <div>
                <div v-for="ma,i in tasca.necessary_material">
                  <div
                    style="
                      display:flex;
                      justify-content:space-between;
                    "
                  >
                    <input v-model="tasca.necessary_material[i]">
                    <button @click="tasca.necessary_material.splice(i,1)">eliminar material</button>
                  </div>
                </div>
              </div>
              <button @click="afegir_material(tasca)">afegir material</button>
            </td>
          </tr>
          <tr>
            <th>Accions</th>
            <td>
              <div>
                <div v-for="ac,i in tasca.actions_to_do"
                  style="
                    display:flex;
                    justify-content:space-between;
                  "
                >
                  <div style="display:flex">
                    <span>{{i+1}}:</span>
                    <textarea v-model="tasca.actions_to_do[i]"></textarea>
                  </div>
                  <button @click="tasca.actions_to_do.splice(i,1)">eliminar acció</button>
                </div>
              </div>
              <button @click="tasca.actions_to_do.push('acció sense nom')">afegir acció</button>
            </td>
          </tr>
          <tr><th>Cost (eur)<td><input type=number v-model.number="tasca.cost_eur" step="0.01"></tr>
          <tr><th>Periodicitat (dies)<td><input type=number v-model.number="tasca.periodicitat_dies"></tr>
          <tr>
            <th>Historial</th>
            <td>
              <div>
                <div v-for="hi,i in tasca.historial">
                  <input type=date v-model="tasca.historial[i]">
                  <button @click="tasca.historial.splice(i,1)">eliminar historial</button>
                </div>
              </div>
              <button @click="tasca.historial.push(new Date().toISOString().substring(0,10))">afegir historial</button>
            </td>
          </tr>

          <tbody v-if="tasca.periodicitat_dies">
            <tr v-for="um in [get_manteniment(tasca)]">
              <th>Següent manteniment</th>
              <td>
                <div v-if="um.next">
                  {{um.next}}
                  <span v-for="dies in [days_to(um.next)]">
                    (d'aquí {{dies}} dies)
                    <span v-if="dies<0" class=warning>
                      PASSAT
                    </span>
                  </span>
                </div>
                <div v-if="!um.next" class=warning>
                  Quant abans
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!--pestanya resum_tasques_periodiques-->
    <div v-if="pestanya_actual=='resum_tasques_periodiques'">
      <h3>Resum tasques periòdiques</h3>
      <div>
        <table border=1 style="text-align:center">
          <thead>
            <tr>
              <th>Aparell</th>
              <th>Tasca</th>
              <th>Periodicitat (dies)</th>
              <th>Últim manteniment</th>
              <th>Següent manteniment</th>
            </tr>
          </thead>
          <tr v-for="row in taula_resum_tasques_periodiques">
            <td>
              <a href="#"
                @click="
                  tasca=row.tasca;
                  aparell=row.aparell;
                  pestanya_actual='inici';
                "
              >
                {{row.aparell.nom}}
              </a>
            </td>
            <td>
              <a href="#"
                @click="
                  tasca=row.tasca;
                  aparell=row.aparell;
                  pestanya_actual='inici';
                "
              >
                {{row.tasca.nom}}
              </a>
            </td>
            <td>{{row.tasca.periodicitat_dies}}</td>
            <td>
              <div v-for="um in [get_manteniment(row.tasca)]">
                <div v-if="um.ultim">
                  {{um.ultim}}
                </div>
                <div v-if="!um.ultim" class=warning>
                  Mai
                </div>
              </div>
            </td>
            <td>
              <div v-for="um in [get_manteniment(row.tasca)]">
                <div v-if="um.next">
                  {{um.next}}
                  <span v-for="dies in [days_to(um.next)]">
                    (d'aquí {{dies}} dies)
                    <span v-if="dies<0" class=warning>
                      PASSAT
                    </span>
                  </span>
                </div>
                <div v-if="!um.next" class=warning>
                  Quant abans
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<script>//Estructura
  class Sistema{
    constructor(){
      this.nom="Sistema sense nom";
      this.lloc="carrer, ciutat";
      this.descripcio="comentaris";
      this.dimensioning=[
        "caracteristíca: valor unitat",
      ];
      this.aparells=[
        new Aparell(),
      ];
    }
  }

  class Aparell{
    constructor(){
      this.nom="Aparell sense nom";
      this.dimensioning=[
        "caracteristíca: valor unitat",
      ];
      this.tasques=[
        new Tasca(),
      ];
    }
  }

  class Tasca{
    constructor(){
      this.nom="Tasca sense nom";
      this.descripcio="Descripció de la tasca";
      this.responsable="ICRA";
      this.necessary_material=[
        "material necessari sense nom",
      ];
      this.actions_to_do=[
        "acció sense nom",
      ];
      this.cost_eur=0;
      this.periodicitat_dies=0;
      this.historial=[];
    }
  }
</script>

<script>//Vue
  let app = Vue.createApp({
    data(){return{
      //tots els sistemes de la base de dades
      metadata_sistemes:null,

      //sistema carregat
      id:0,
      sistema:null,
      aparell:null,
      tasca:null,

      //gui
      pestanyes:[
        "inici",
        "resum_tasques_periodiques",
      ],
      pestanya_actual:"inici",
    }},

    methods:{
      days_to(time_str){
        let dn = new Date(); //date now
        let df = new Date(time_str); //date future
        let dd = (df.getTime()-dn.getTime())/1000/86400; //dies
        return Math.ceil(dd);
      },

      //class methods here for easy parsing from json
      afegir_aparell(sistema){
        let ap = new Aparell();
        sistema.aparells.push(ap);
        this.aparell = ap;
        this.tasca = null;
      },

      afegir_tasca(aparell){
        let ta = new Tasca();
        aparell.tasques.push(ta);
        this.tasca = ta;
      },

      afegir_material(tasca){
        tasca.necessary_material.push("material necessari sense nom");
      },

      get_manteniment(tasca){
        let ultim = false;
        let next  = false;

        if(tasca.historial.length){
          ultim = tasca.historial.at(-1);

          let du = new Date(ultim);
          let dn = new Date(du.getTime()+tasca.periodicitat_dies*86400e3);
          next = dn.toISOString().substring(0,10);
        }

        return {ultim,next};
      },

      //file managing
      nou(){
        this.sistema = new Sistema();
        this.aparell = null;
        this.tasca = null;

        //nova id
        let id = Math.max(0,...this.metadata_sistemes.map(sis=>sis.id))+1;
        this.id = id;
      },

      //carrega un sistema concret
      carregar(id){
        fetch(`db/get_sistema.php?id=${id}`).then(r=>r.json()).then(r=>{
          r = r[0];
          this.id=r.id;
          this.sistema=JSON.parse(r.json);
          this.aparell=null;
          this.tasca=null;

          if(this.sistema.aparells && this.sistema.aparells.length){
            this.aparell = this.sistema.aparells[0];
            if(this.aparell.tasques && this.aparell.tasques.length){
              this.tasca=this.aparell.tasques[0];
            }
          }
        });
      },

      //guardar sistema actual (o crear un de nou)
      guardar(){
        if(!confirm("guardar el sistema actual?")){
          return;
        }

        let payload = JSON.stringify(this.sistema);

        let body = new FormData();
        body.append('id',this.id);
        body.append('json',payload);

        fetch(
          'db/guardar.php',
          {method:'POST',body},
        ).then(r=>
          r.text()
        ).then(success=>{
          console.log(success);
          alert(success); //"OK"
          if(success!="OK"){
            throw(success);
          }
          fetch_metadata_sistemes();
        }).catch(err=>{
          alert(err);
        });
      },

      //esborrar un sistema
      esborrar(id){
        if(!confirm(`Esborrar el sistema (id:${id}) (NO ES POT DESFER!)?`)){
          return;
        }

        fetch(
          `db/esborrar.php?id=${id}`,
        ).then(r=>
          r.text()
        ).then(success=>{
          console.log(success);
          alert(success); //"OK"
          if(success!="OK"){
            throw(success);
          }
          fetch_metadata_sistemes();
        }).catch(err=>{
          alert(err);
        });
      },
    },

    computed:{
      taula_resum_tasques_periodiques(){
        let taula=[];

        let aparells = this.sistema.aparells.forEach(aparell=>{
          aparell.tasques.forEach(tasca=>{
            if(tasca.periodicitat_dies){
              let fila={
                tasca,
                aparell,
              };
              taula.push(fila);
            }
          });
        });

        return taula;
      },
    },

    mounted(){
      //this.carregar(0);
      fetch_metadata_sistemes();
    },
  }).mount("#app");

  //carrega un sistema concret
  function fetch_metadata_sistemes(){
    fetch(`db/metadata_sistemes.php`).then(r=>r.json()).then(arr=>{
      app.metadata_sistemes = arr;
    });
  }
</script>
