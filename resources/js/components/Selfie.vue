<template>
    <div class="container">
        <div class="content d-grid align-content-between text-center">
            <div class="row  justify-content-center d-flex">
                <h2 class="mb-5">Take Selfie</h2>
            </div>            
                <div class="contentVideo">
                    <video id="myVideo" class="inputVideo" ref="video" playsinline=""></video>
                </div>   
                <div class="contentVideo justify-content-center d-flex" hidden>            
                    <canvas id="photoTaken" ref="canvas" hidden></canvas>
                </div>
                <img :src="imgSrc" v-if="imgSrc" />
            <div class="row  justify-content-center d-flex">
                <div class="contentBtn">
                    <b-button class="subBtn mt-5" type="button" @click="takeSelfie" variant="success">Take</b-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {    
  name: 'VerifyID',
  data() {
      return {
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
        imgSrc: null,
      }
  },
    mounted(){
        this.setupPage();
        console.log(this.$store.state.idVerification);
    },
  methods:{
        async setupPage() {
            var vm = this ;
            const state = {
                backend: 'wasm'
            }; 
            
            await tf.setBackend(state.backend);
            await vm.setupCamera();
            vm.video.play();         
            vm.canvas = vm.$refs.output;
            
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
        takeSelfie(){
            var vm = this;
            let formData = new FormData();  

            vm.$refs.canvas.height = vm.video.videoHeight;
            vm.$refs.canvas.width = vm.video.videoWidth;

            const context = vm.$refs.canvas.getContext('2d');            
            context.drawImage(vm.$refs.video, 0, 0);
            
            const canvas = document.getElementById("photoTaken").toDataURL("image/jpeg");
            canvas.replace('data:image/jpeg;base64,', '');
            let blobToFile = vm.dataURItoBlob(canvas);
            
            const cfile = new File([blobToFile], "selfie-"+this.$store.state.idVerification + ".jpg",
                    {type: "image/jpeg", lastModified: Date.now()});
            
            formData.append('photo', cfile);
            formData.append('idverification', this.$store.state.idVerification);

            vm.$auth.post('/api/uploadPhoto', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        }
                }).then(result => {                    
				 this.$router.push('/rec-video');
                }).catch(err => {
                    
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

.subBtn {
    background-color: #e98230;
    border-color: #e98230;
    border-radius: 0px;
    width: 7em;
    height: 2.5em;
}

.subBtn:hover {
    background-color: #c05f11;
    border-color: #c05f11;
    border-radius: 0px;
    width: 8em;
    height: 3em;
    font-size: 1.1em;
}


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
    margin-bottom: 2em;
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
    width: 100%;
    margin-top: 2em;
    border-radius: 0.5em;
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
        left: 0;
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
        left: 0;
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