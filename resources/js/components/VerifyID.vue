<template>
    <div class="container">
        <div class="content text-center">
            <div class=" row d-flex justify-content-center">
                <div class="alertVerification">
                    <b-alert v-model="showAlert" variant="danger">
                        This id already verfied
                        <button type="button" @click="showAlert=false" aria-label="Close">×</button>
                    </b-alert>    
                </div>            
            </div>
            <div class="row d-flex justify-content-center">
                <h2>Verification</h2>
            </div>
            <div class="row d-flex justify-content-center">
                <form @submit="submitId">
                <div class="">
                    <b-input id="idVerification" class="col-md-6" size="lg" placeholder="Enter your code" v-model="idVerification" required></b-input>
                    <div>
                        <span class="form-text text-danger">{{ message }}</span>  
                    </div>
                </div>
                <b-button class="subBtn" type="submit" variant="success">Submit</b-button>
            </form>
            </div>
            
        </div>
    </div>
</template>

<script>
export default {    
  name: 'VerifyID',
  data() {
      return {
          idVerification:'',
          message: '',
          showAlert: false,
      }
  },
  mounted(){
      this.$store.dispatch('success', false); 
  },
  methods:{
      submitId(e){          
        e.preventDefault();
        console.log(this.idVerification.length);
        if(this.idVerification.length < 5){
            this.message = 'the verification id is less than 5'
        }else if( 15 < this.idVerification.length ){
            this.message = 'the verification id is more than 15'
        }else{
            this.$store.dispatch('idVerification',this.idVerification); 
            let formData = new FormData();  
            formData.append('idverification', this.$store.state.idVerification);

            this.$auth.post('/api/checkIdExist', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        }
                }).then(result => {      
                   this.$router.push('/verify-identite');
                }).catch(err => {
                    this.showAlert = true
                });

            
            
        }
      },
  },
}
</script>

<style scoped>

h2 {
  font-family: 'Open Sans', sans-serif;
  font-weight: 800;
  font-size: 40px;
  color: #2C3E50;
  margin: 10px 0;
}

h3 {
  font-family: 'Open Sans', sans-serif;
  font-weight: 400;
  font-style: italic;
  font-size: 15px;
  color: #666;
  margin-bottom: 20px;
}

h1 {
  margin: 40px 0;
  font-weight:800;
  font-size:48px;
  font-family: 'Open Sans', sans-serif;
  color: #2FC877;
}

#idVerification {
    width: 25%;
    display: inline-flex;
    margin: 1.5rem 0 0 0;
}

.content {
    padding: 10% 0;
    background-color: #fff;
    margin: 15%;
    border-radius: 0.5em;
}

.subBtn {
    background-color: #2FC877;
    border-color: #2FC877;
    border-radius: 5rem;
    width: 7em;
    margin: 1.5rem;
}

.alertVerification {
    width: 50%;
}

.alertVerification button {
    float: right !important;
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    color: #000 !important;
    text-shadow: 0 1px 0 #fff !important;
    opacity: .5 !important;
}
.alertVerification button {
    padding: 0 !important;
    background-color: transparent !important;
    border: 0 !important;
}

.alertVerification button {
    position: absolute !important;
    top: 0 !important;
    right: 0 !important;
    z-index: 2 !important;
    padding: 0.75rem 1.25rem !important;
    color: inherit !important;
}

@media (max-width: 980px) {
    .content {
        padding: 10% 0;
        background-color: #fff;
        margin: 30% 0;
        border-radius: 0.5em;
    }   

    .alertVerification {
        width: 70%;
    }

    #idVerification {
        width: 40%;
        display: inline-flex;
        margin: 1.5rem 0 0 0;
    }

    .subBtn {
        background-color: #2FC877;
        border-color: #2FC877;
        border-radius: 5rem;
        width: 10em;
        margin: 1.5rem;
    }
}

@media (max-width: 480px) {
    #idVerification {
        width: 60%;
    }
}

@media (max-width: 320px) {
    h2 {
        font-size: 28px;
    }
}



</style>