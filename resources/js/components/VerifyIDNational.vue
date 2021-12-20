<template>
    <div class="container">
         <div v-if="!idtypeSelected" class="content justify-content-center text-center">
            <h2 class="py-5">Choose type ID</h2>
            <div class="row justify-content-center d-flex">
                <div class="col-md-5 col-lg-4 col-sm-5 col-5 mx-2 content-card" @click="selectTypeVerification(1)">
                    <b-card id="header-card" img-height="300" img-width="175" img-alt="Image" img-top>
                        <img  :src="'/assets/identity-card.png'">
                        <template #footer id="footer-card">
                            <small class="text-muted">Idenity Card</small>
                        </template>
                    </b-card>
                </div>
                <div class="col-md-5 col-lg-4 col-sm-5 col-5 mx-2 content-card" @click="selectTypeVerification(2)">
                    <b-card id="header-card" img-height="300" img-width="175" img-alt="Image" img-top>
                        <img  :src="'/assets/passport.png'">
                        <template #footer id="footer-card">
                            <small class="text-muted">Passport</small>
                        </template>
                    </b-card>
                </div>
            </div>
        </div>
        <div v-if="idtypeSelected && identityCard">
            <div v-if="!isBack" class="content text-center d-grid align-content-between">
                <div class="row">
                    <h2 class="py-3">FRONT ID</h2>
                </div>                
                <div class="justify-content-center  d-flex">
                    <div v-if="showModal" @close="showModal = false"> </div>
                    <div class="contentVideo">
                        <video id="myVideo" class="inputVideo" ref="video" playsinline=""></video>
                    </div>    
                    
                    <div class="contentVideo outputVideo justify-content-center d-flex">            
                        <canvas id="canvasVideoFront" ref="output"></canvas>
                    </div>
                    <div class="contentVideo outputVideo justify-content-center d-flex">            
                        <img :src="'/assets/cadre-id.png'" class="imgMask" >
                    </div>
                </div>
                <div class="row">
                    <div class="contentBtn justify-content-center">            
                        <button id="myBtn" @click="saveID">Capture</button> 
                    </div>
                </div>                
            </div>
            <div v-else class="content text-center d-grid align-content-between">
                <div class="row">
                    <h2 class="py-3">Back ID</h2>
                </div> 
                <div class="justify-content-center  d-flex">
                    <div v-if="showModal" @close="showModal = false"> </div>
                    <div class="contentVideo">
                        <video id="myVideo" class="inputVideo" ref="video" playsinline=""></video>
                    </div>    
                    
                    <div class="contentVideo outputVideo justify-content-center d-flex">            
                        <canvas id="canvasVideoBack" ref="output"></canvas>
                    </div>
                    <div class="contentVideo outputVideo justify-content-center d-flex">            
                        <img :src="'/assets/cadre-id.png'" class="imgMask" >
                    </div>
                </div>
                <div class="row">
                    <div class="contentBtn justify-content-center">            
                        <button id="myBtn" @click="saveID">Capture</button> 
                    </div>
                </div>   
            </div>
        </div>
        <div v-if="idtypeSelected && passport">
            <div class="content d-grid align-content-between text-center">
                <div class="row">
                    <h2 class="py-3">Passport</h2>
                </div>                 
                <div class="justify-content-center  d-flex">
                    <div v-if="showModal" @close="showModal = false"> </div>
                    <div class="contentVideo">
                        <video id="myVideo" class="inputVideo" ref="video" playsinline=""></video>
                    </div>   
                    
                    <div class="contentVideo outputVideo justify-content-center d-flex">            
                        <canvas id="canvasVideoFront" ref="output"></canvas>
                    </div>
                    x
                </div>
                <div class="row">
                    <div class="contentBtn justify-content-center">            
                        <button id="myBtn" @click="savePassport">Capture</button> 
                    </div>
                </div>   
            </div>
        </div>
    </div>
</template>

<script>
    export default {        
        name: 'RecVideo',
        template: '#capture',
        props: ['facingmode'],
        data(){
            return{
            video: null,
            prediction: '',
            ctx: null,
            canvas: null,
            model: null,
            showModal: true,
            videoMarginLeftAdjust: null,
            videoHeight: null,
            videoWidth: null,
            capture: false,
            isBack: false,
            idtypeSelected: false,
            identityCard: false,
            passport: false,
            }
        },
        mounted(){
            // this.setupPage()
        },
        methods:{
            selectTypeVerification(value){
                if(value == 1){
                    this.setupPage();
                    this.identityCard = this.idtypeSelected = true;
                }else{
                    this.setupPage();
                    this.passport =  this.idtypeSelected = true;
                }
            },
            // async renderPrediction(){
            //     var vm = this
            //     const returnTensors = false;
            //     const flipHorizontal = true;
            //     const annotateBoxes = true;
            //     const predictions = await vm.model.estimateFaces(
            //     vm.video, returnTensors, flipHorizontal, annotateBoxes, );                
                
            //     if(predictions.length == 0){
            //         this.capture = false;
            //         vm.ctx.clearRect(0, 0, vm.canvas.width, vm.canvas.height);
            //         vm.ctx.beginPath();
            //         vm.ctx.lineWidth = 4;
            //         vm.ctx.strokeStyle = "red";
            //         vm.ctx.rect(100, 100, 100, 100);
            //         vm.ctx.stroke();
            //     }

            //     this.capture = true;
            //     vm.ctx.clearRect(0, 0, vm.canvas.width, vm.canvas.height);
            //     for (let i = 0; i < predictions.length; i++) {
            //         if (returnTensors) {
            //             predictions[i].topLeft = predictions[i].topLeft.arraySync();
            //             predictions[i].bottomRight = predictions[i].bottomRight.arraySync();
            //             if (annotateBoxes) {
            //             predictions[i].landmarks = predictions[i].landmarks.arraySync();
            //             }
            //         }
            //         try {
            //             vm.prediction += 'Probability ' + i + ' : ' + predictions[i].probability 
                        
            //         }
            //         catch(err){
            //             vm.prediction= err.message
            //         }

            //         vm.ctx.beginPath();
            //         vm.ctx.lineWidth = 4;
            //         vm.ctx.strokeStyle = "green";
            //         vm.ctx.rect(predictions[i].topLeft[0], predictions[i].topLeft[1], predictions[i].bottomRight[0] - predictions[i].topLeft[0], predictions[i].bottomRight[1] -predictions[i].topLeft[1]);
            //         vm.ctx.stroke();
            //     }  

            //     requestAnimationFrame(vm.renderPrediction);

            // },
            async setupPage() {
                var vm = this ;
                const state = {
                    backend: 'wasm'
                }; 
                
                await tf.setBackend(state.backend);
                await vm.setupCamera();
                vm.video.play();
                
                this.videoWidth = vm.video.videoWidth;
                this.videoHeight = vm.video.videoHeight;
                
                vm.canvas = vm.$refs.output;
                vm.canvas.width = this.videoWidth;
                vm.canvas.height = this.videoHeight;
                vm.ctx = vm.canvas.getContext('2d');
                vm.ctx.fillStyle = "rgba(255, 0, 0, 0.5)";
                vm.videoHeight = window.innerHeight;
                vm.videoMarginLeftAdjust =   ( window.innerWidth - this.videoWidth) / 2;
                vm.model = await blazeface.load();
                
                // vm.renderPrediction();
            },
            async setupCamera() {    
                var vm = this
                vm.video = vm.$refs.video;
                
                const stream = await navigator.mediaDevices.getUserMedia({
                'audio': false,
                'video': { facingMode: vm.facingmode },
                });
                vm.video.srcObject = stream;
                
                return new Promise((resolve) => {
                    vm.video.onloadedmetadata = () => {
                    resolve(vm.video);
                    };
                });
            },
            saveID(){
                var vm = this;
                let formData = new FormData();  

                vm.$refs.output.height = vm.video.videoHeight;
                vm.$refs.output.width = vm.video.videoWidth;

                const context = vm.$refs.output.getContext('2d');                                 
                context.drawImage(vm.$refs.video, 0, 0);
                if(!vm.isBack){
                    const canvas = document.getElementById("canvasVideoFront").toDataURL("image/jpeg");
                    canvas.replace('data:image/jpeg;base64,', '');
                    let blobToFile = vm.dataURItoBlob(canvas);
                    const cfile = new File([blobToFile], "front-id-"+this.$store.state.idVerification + ".jpg",
                            {type: "image/jpeg", lastModified: Date.now()});
                    
                    formData.append('photo', cfile);
                    formData.append('idverification', this.$store.state.idVerification);
    
                    vm.$auth.post('/api/uploadPhoto', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                }
                        }).then(result => {      
                            this.isBack = true;  
                        }).catch(err => {
                           console.error(err.message); 
                        });
                }else{
                    const canvas = document.getElementById("canvasVideoBack").toDataURL("image/jpeg");
                    canvas.replace('data:image/jpeg;base64,', '');
                    let blobToFile = vm.dataURItoBlob(canvas);
                    
                    const cfile = new File([blobToFile], "back-id-"+this.$store.state.idVerification + ".jpg",
                            {type: "image/jpeg", lastModified: Date.now()});
                    
                    formData.append('photo', cfile);
                    formData.append('idverification', this.$store.state.idVerification);
    
                    vm.$auth.post('/api/uploadPhoto', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                }
                        }).then(result => {                    
                            this.$router.push('/take-selfie');
                            vm.isBack = false;
                        }).catch(err => {
                            
                    });
                }
            },
            savePassport(){
                var vm = this;
                let formData = new FormData();  

                vm.$refs.output.height = vm.video.videoHeight;
                vm.$refs.output.width = vm.video.videoWidth;

                const context = vm.$refs.output.getContext('2d');                                 
                context.drawImage(vm.$refs.video, 0, 0);

                const canvas = document.getElementById("canvasVideoFront").toDataURL("image/jpeg");
                canvas.replace('data:image/jpeg;base64,', '');
                let blobToFile = vm.dataURItoBlob(canvas);
                const cfile = new File([blobToFile], "passport-"+this.$store.state.idVerification + ".jpg",
                        {type: "image/jpeg", lastModified: Date.now()});
                
                formData.append('photo', cfile);
                formData.append('idverification', this.$store.state.idVerification);

                vm.$auth.post('/api/uploadPhoto', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            }
                    }).then(result => {      
                        this.$router.push('/take-selfie');
                    }).catch(err => {
                        console.error(err.message); 
                    });
            },
            dataURItoBlob(dataURI) {
                var byteString = atob(dataURI.split(',')[1]);
                var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
                var arrayBuffer = new ArrayBuffer(byteString.length);
                var _ia = new Uint8Array(arrayBuffer);
                for (var i = 0; i < byteString.length; i++) {
                    _ia[i] = byteString.charCodeAt(i);
                }
                var dataView = new DataView(arrayBuffer);
                var blob = new Blob([dataView], {type: mimeString});
                return blob;
            },
            
        }
    }
</script>

<style scoped>

.content-card {
    cursor: pointer;
}

.card-body{
    padding: 0 !important;
}

.card-footer {
   background-color: lightskyblue !important;
    color: white !important;
    height: 4em !important;
    display: flex !important;
    justify-content: center !important;
    }

.text-muted {
    color: #303437 !important;
    font-weight: 800;
    align-content: center;
    display: grid;
}

#myVideo {
    width: 700px;
    height: 600px;  
}

#canvasVideo {
    width: 700px;
    height: 600px;  
}

.inputVideo{
     -webkit-transform: scaleX(-1);
    transform: scaleX(-1);
    width: auto;
    height: auto;
}

.contentVideo {
  position: fixed;
  top: 130px;
  left: 0;
  width: 100%;
  padding: 20px;  
}

.imgMask {
    width: 700px;
    height: 525px;
    margin-top: 38px;
}
/* Add some content at the bottom of the video/page */
.contentBtn {
    color: #f1f1f1;
    width: 100%;  
    height: 3.2em;
    margin-bottom: 1em;
}

/* Style the button used to pause/play the video */
#myBtn {
  width: 200px;
  font-size: 18px;
  padding: 10px;
  border: none;
  background: #000;
  color: #fff;
  cursor: pointer;
}

#myBtn:hover {
  background: #ddd;
  color: black;
}
h2 {
  font-family: 'Open Sans', sans-serif;
  font-weight: 800;
  font-size: 40px;
  color: #2C3E50;
  margin: 30px 0;
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
    margin: 1.5rem;
}

.content {
    background-color: #fff;
    height: 800px;
    width: 96%;
    margin-top: 2em;
    border-radius: 0.5em;
}

.subBtn {
    background-color: #2FC877;
    border-color: #2FC877;
    border-radius: 5rem;
    width: 7em;
}



@media (max-width: 995px) and (min-width: 769px) {
    .card-footer {
        height: 3em !important;
    }

    .content-card img {
       width: 100%;
    }

    .content {
        margin-top: 4em;
    }

    .imgMask {
        width: 640px;
        height: 480px;
        margin-top: 0px;
    }

    .contentVideo {
        left: auto;
        top: 170px;
    }

    .contentBtn {
        margin-bottom: 5em;
    }

    #myVideo {
        width: 640px;
        height: 480px;
    }

}

@media (max-width: 768px) {
    .card-footer {
        height: 3em !important;
    }

    .content-card img {
       width: 100%;
    }

    .content {
        margin-top: 4em;
        height: 740px;
    }

    .imgMask {
        width: 490px;
        height: 368px;
        margin-top: 0px;
    }

    #myVideo {
        width: 490px;
        height: 368px;
    }

    #canvasVideoFront {
        width: 490px;
        height: 368px;
    }

    .contentVideo {
        position: fixed;
        top: 200px;
        left: auto;
        width: 100%;
        padding: 20px;  
    }

    .contentBtn {
        margin-bottom: 6em;
    }

}

@media (max-width: 560px) {
    .content {
        height: 600px;
        margin-top: 6em;
    }

    .imgMask {
       width: 410px;
    height: 309px;
    margin-top: 15px;
    }

    #myVideo {
        width: 410px;
        height: 340px;
    }

    #canvasVideoFront {
        width: 410px;
        height: 340px;
    }

    .contentBtn {
        margin-bottom: 3em;
    }

    h2{
        font-size: 26px;
        margin: 30px 0;
    }
}

@media (max-width: 480px) {
    .content {
        height: 500px;
        margin-top: 6em;
    }

    .imgMask {
        width: 310px;
        height: 232px;
        margin-top: 54px;
    }

    #myVideo {
        width: 310px;
        height: 340px;
    }

    #canvasVideoFront {
        width: 310px;
        height: 340px;
    }

    .contentBtn {
        margin-bottom: 3em;
    }

    h2{
        font-size: 26px;
        margin: 30px 0;
    }

    .contentVideo{
        top: 140px;
    }
}

@media (max-width: 370px) {
    .content {
        height: 410px;
    }


    .contentVideo{
        top: 110px;
    }

    .imgMask {
        width: 230px;
        height: 174px;
        margin-top: 83px;
    }

    #myVideo {
        width: 230px;
        height: 340px;
    }

    #canvasVideoFront {
        width: 230px;
        height: 340px;
    }

    h2{
        font-size: 22px;
    }
}
</style>