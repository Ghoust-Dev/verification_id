<template>
    <div class="container py-4">
        <!-- <canvas id="target" width="800px" height="400px" style="margin: auto" ref="canvaMask"></canvas> -->
         <div v-if="!idtypeSelected" class="content justify-content-center text-center">
            <h2 class="py-5">Select type ID</h2>
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
            <div class="content text-center d-grid align-content-center">
                <div class="row">
                    <h2 v-if="!isBack"  class="py-2">FRONT ID</h2>
                    <h2 v-else class="py-2">Back ID</h2>
                </div>                
                <div  class="">
                    <div v-if="showModal" @close="showModal = false"> </div>
                    <div id="video_box">
                        <div data-v-cc8d46de="" id="video_overlays" class="imgMask" @click="changeCam" style="
                            height: 10%;
                            top: 88%;
                            z-index: 500000;
                        ">
                            <img data-v-cc8d46de="" else="" src="/assets/camera-icon.png">
                        </div>
                        <div id="video_overlays" class="imgMask">            
                            <img v-if="portrait" :src="'/assets/cadre-id-portrait.png'"   ref="mask">
                            <img else :src="'/assets/cadre-id.png'"   ref="mask">
                        </div>
                        <video id="myVideo" class="inputVideo" ref="video" playsinline=""></video>
                    </div>    
                    <div class="row mt-2">
                        <div class="contentBtn justify-content-center">            
                            <button id="myBtn" @click="saveID">Capture</button> 
                        </div>
                    </div> 
                    <div class="video-options">
                        <b-form-select v-model="cameraSelected">
                            <b-form-select-option v-for="cam in optionsCam"  :key="cam.id" :value="cam.id">{{cam.value}}</b-form-select-option>
                        </b-form-select>
                    </div>   
                    
                    <div class="outputVideo" hidden>            
                        <canvas v-if="!isBack" id="canvasVideoFront" ref="output"></canvas>
                        <canvas v-else id="canvasVideoBack" ref="output"></canvas>
                    </div>            
                    
                </div>
            </div>
        </div>
        <div v-if="idtypeSelected && passport">
            <div class="content text-center d-grid align-content-center">
                <div class="row">
                    <h2 class="py-2">Passport</h2>
                </div>                
                <div  class="">
                    <div v-if="showModal" @close="showModal = false"> </div>
                    <div id="video_box">
                        <div id="video_overlays" class="imgMask">            
                            <img :src="'/assets/cadre-id.png'"  >
                        </div>
                        <video id="myVideo" class="inputVideo" ref="video" playsinline=""></video>
                    </div>    
                    <div class="row">
                        <div class="contentBtn justify-content-center">            
                            <button id="myBtn" @click="savePassport">Capture</button> 
                        </div>
                    </div>    
                    
                    <div class="outputVideo" hidden>            
                        <canvas id="canvasVideoFront" ref="output"></canvas>
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
            portrait:false,
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
            optionsCam: [],
            cameraSelected: 0,
            }
        },
        async mounted(){
            const orientation = await window.screen.orientation.type
             console.log('portrait screen '+window.screen.orientation.type);
                if (orientation === "portrait-primary") {
                    this.portrait = true;
                    console.log('portrait screen '+this.portrait);
                } else if (orientation === "landscape-primary") {
                    this.portrait = false;
                    console.log('landscape screen '+this.portrait);
                }

            window.addEventListener(
                "orientationchange",
                await this.handleOrientationChange
            );
        },
        methods:{
            handleOrientationChange() {
                const orientation = window.screen.orientation.type
                    console.log('portrait screen '+orientation);
                if (orientation === "portrait-primary") {
                    this.portrait = true;
                    console.log('portrait screen '+this.portrait);
                } else if (orientation === "landscape-primary") {
                    this.portrait = false;
                    console.log('landscape screen '+this.portrait);
                }
            },
            async getCamera(){ 
                var vm = this;
                 vm.optionsCam = [];
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === 'videoinput');
                videoDevices.map(videoDevice => {
                    vm.optionsCam[videoDevice.deviceId] = videoDevice.label;
                    vm.optionsCam.push(
                        {
                            id: videoDevice.deviceId,
                            value: videoDevice.label
                        }
                    );
                });
            },
            selectTypeVerification(value){                
                this.handleOrientationChange();
                console.log('portrait screen ');
                if(value == 1){
                    this.setupPage();
                    this.identityCard = this.idtypeSelected = true;
                }else{
                    this.setupPage();
                    this.passport =  this.idtypeSelected = true;
                }
            },
            async setupPage() {
                var vm = this ;
                const state = {
                    backend: 'wasm'
                }; 
                
                await tf.setBackend(state.backend);
                await vm.getCamera();
                await vm.setupCamera();
                vm.video.play();
                
                this.videoWidth = vm.video.videoWidth;
                this.videoHeight = vm.video.videoHeight;
            },
            async setupCamera() {    
                var vm = this
                vm.video = vm.$refs.video;
                
                const stream = await navigator.mediaDevices.getUserMedia({
                'audio': false,
                'video': "environment",                 
                });
                vm.video.srcObject = stream;
                
                return new Promise((resolve) => {
                    vm.video.onloadedmetadata = () => {
                    resolve(vm.video);
                    };
                });
            },
           async changeCam() {
                var vm = this
                vm.video = vm.$refs.video;
                vm.setupPage();
                if ('mediaDevices' in navigator && navigator.mediaDevices.getUserMedia) {
                    // const videoStream = await await navigator.mediaDevices.getUserMedia({
                    // 'audio': false,
                    // 'video': "environment",
                    // });

                    console.log('camera selected '+vm.cameraSelected);
                    const updatedConstraints = {
                        'audio': false,
                        'video': {
                            'exact': {
                                exact: vm.cameraSelected,
                            },
                        }
                    };
                    const stream = await navigator.mediaDevices.getUserMedia(updatedConstraints);
                    vm.video.srcObject = stream;
                    
                    return new Promise((resolve) => {
                        vm.video.onloadedmetadata = () => {
                            resolve(vm.video);
                        };
                    });
                }
                vm.video.play();
                
                this.videoWidth = vm.video.videoWidth;
                this.videoHeight = vm.video.videoHeight;
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

@media screen and (orientation:landscape) {
    .content {
        height: 800px;
    }
}

@media screen and (orientation:portrait) {
    .content {
        height: 90vh;
    }

    #myVideo {
        width: 100%;
        height: 100%;
    }

    @media (max-width: 768px) {
        .content {
            height: 90vh;
        }

        #myVideo {
            width: 100%;
            height: 100%;
        }

    }

    @media (max-width: 560px) {
        .content {
            height: 90vh;
        }

        #myVideo {
            width: 100%;
            height: 100%;
        }
    }

    @media (max-width: 480px) {
        .content {
            height: 90vh;
        }

        #myVideo {
            width: 100%;
            height: 100%;
        }
    }

    @media (max-width: 370px) {
        .content {
            height: 90vh;
        }

        #myVideo {
            width: 100%;
            height: 100%;
        }
    }
}


#video_box{
    position:relative;
    -webkit-box-align:center;
    -webkit-box-pack:center;
    box-align:center;
    box-pack:center;
    display:-webkit-box;
    overflow: hidden;
}

#video_box_back{
    position:relative;
    -webkit-box-align:center;
    -webkit-box-pack:center;
    box-align:center;
    box-pack:center;
    display:-webkit-box;
}

.content-card {
    cursor: pointer;
}

.text-muted {
    color: #303437 !important;
    font-weight: 800;
    align-content: center;
    display: grid;
}

#video_overlays {
    position: absolute;
    width: 100%;
    height: 98.8%;
    z-index: 300000;
    text-align: center;
    top: 0%;
}

#video_overlays_back {
    position: absolute;
    width: 100%;
    height: 98.8%;
    z-index: 300000;
    text-align: center;
    top: 0%;
}

.card-footer {
   background-color: lightskyblue !important;
    color: white !important;
    height: 4em !important;
    display: flex !important;
    justify-content: center !important;
}

.inputVideo{
     -webkit-transform: scaleX(-1);
    transform: scaleX(-1);
    width: auto;
    height: auto;
}

.video_back{
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

.imgMask img {    
    max-width: 100%;
    max-height: 100%;
}

.imgMask_back img {    
    max-width: 100%;
    max-height: 100%;
}

.contentBtn {
    color: #f1f1f1;
    width: 100%;  
    height: 3.2em;
}

#myBtn {
  width: 200px;
  font-size: 18px;
  padding: 10px;
  border: none;
  background: #000;
  color: #fff;
  cursor: pointer;
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

.content {
    background-color: #fff;
    width: 96%;
    margin-top: 2em;
    border-radius: 0.5em;
}

@media (max-width: 995px) and (min-width: 769px) {
    .content-card img {
       width: 100%;
    }

    .content {
        margin-top: 4em;
    }

    #video_overlays {
        position: absolute;
        width: 100%;
        height: 99%;
        z-index: 300000;
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
    .content-card img {
       width: 100%;
    }

    .content {
        margin-top: 4em;
        height: 740px;
    }

    #video_overlays {
        position: absolute;
        width: 100%;
        height: 98%;
        z-index: 300000;
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

    #myVideo {
        width: 100%;
        height: 100%;
    }

    #video_overlays {
        position:absolute;
        width: 100%;
        height: 100%;
        z-index:300000;
        text-align:center;
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

    #myVideo {
        width: 100%;
        height: 100%;
    }

    #video_overlays {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 300000;
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

    #video_overlays {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 300000;
    }

    #myVideo {
        width: 100%;
        height: 100%;
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