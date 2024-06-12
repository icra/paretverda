<?php
  include'auth.php';
?>
<!doctype html><html><head>
  <meta charset=utf8>
  <title>ICRA Paret Verda</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <style>
    table{
      border-collapse:collapse;
      width:100%;
    }
    a[current=true]{
      background:yellow;
    }
  </style>
</head><body>
<h1>icra: manteniment paret verda (en desenvolupament)</h1>

<div id=app>
  <div>
    <button @click="nou()">nou</button> |
    <button @click="guardar()" :disabled="sistema==null">guardar</button> |
    <button @click="carregar()">carregar</button> |
    <a href="db/api.php" target="_blank">veure guardat (format json)</a>
  </div>

  <div
    style="
      display:grid;
      grid-template-columns:33% 33% 33%;
      grid-gap:1px;
    "
  >
    <div v-if="sistema">
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
                  <button @click="sistema.aparells.splice(i,1)">eliminar aparell</button>
                </div>
              </div>
            </div>
            <button @click="afegir_aparell(sistema)">afegir aparell</button>
          </td>
        </tr>
      </table>
    </div>

    <div v-if="sistema && aparell">
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

    <div v-if="sistema && aparell && tasca">
      <h2>Tasca</h2>
      <table border=1>
        <tr>
          <td colspan=2 style="text-align:right">
            <button @click="tasca=null">X</button>
          </td>
        </tr>
        <tr><th>Nom tasca<td><input v-model.text="tasca.nom"></tr>
        <tr><th>Descripció<td><input v-model.text="tasca.descripcio"></tr>
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
              <div v-for="ac,i in tasca.actions_to_do">
                {{i+1}}:
                <input v-model="tasca.actions_to_do[i]">
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
      </table>
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
        "cubell",
        "aigua",
        "guants làtex",
        "ulleres protecció",
      ];
      this.actions_to_do=[
        "Aturar la bomba",
        "Posar el cubell",
        "Desenroscar el filtre",
        "Netejar el filtre",
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
      id:0,
      sistema:null,
      aparell:null,
      tasca:null,
    }},
    methods:{

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

      //file managing
      nou(){
        this.sistema = new Sistema();
        this.aparell=null;
        this.tasca=null;
      },
      carregar(){
        fetch("db/api.php").then(r=>r.json()).then(r=>{
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
      guardar(){
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
        }).catch(err=>{
          alert(err);
        });
      },
    },
    computed:{},
  }).mount("#app");
</script>
