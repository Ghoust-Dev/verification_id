<template>
    <div class="container">
        <div class="content text-center d-grid align-content-center">
            <div class="justify-content-center d-flex">
                <h2>Start recording videos and respect the notes and the time</h2>
            </div>
            
            <div class="my-4 d-flex justify-content-center">
                <div class="contentVideoRecord">
                    <div class="" id="video_box">
                        <div id="video_overlays" class="imgMask">
                            <img v-if="!portrait" :src="'/assets/face-human.png'"   ref="mask">
                            <img else :src="'/assets/face-human-portrait.png'"   ref="mask">
                        </div>
                        <video id="myVideo2" class="inputVideo video-js" ref="video" playsinline=""></video>
                    </div>                    
                </div>  
            </div>
            
            <div v-if="lenghtVideo != 0" class="justify-content-center d-flex">
                <h4>Time: {{lenghtVideo}}s</h4>
            </div>
            <div class="">
                <div v-if="lenghtVideo != 0" class="col-md-12 justify-content-center d-flex">
                    <b-card bg-variant="primary" class="justify-content-center d-flex align-items-center" text-variant="white">
                        <blockquote class="card-blockquote">
                            <p class="justify-content-center d-flex align-items-center">{{ListToDo[listDoit[randomNote]]}}</p>
                        </blockquote>
                    </b-card>
                </div>
                <div class="contentBtn col-md-12 justify-content-center d-flex">         
                    <button id="myBtn" type="button" class="btn btn-info" @click.prevent="startRecording()" v-bind:disabled="isStartRecording">Start Recording</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import 'video.js/dist/video-js.css'
import videojs from 'video.js'
import 'webrtc-adapter'
import RecordRTC from 'recordrtc'
import Record from 'videojs-record/dist/videojs.record.js'
import FFmpegjsEngine from 'videojs-record/dist/plugins/videojs.record.ffmpegjs.js';

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
            randomNote: 0,
            player: '',
            retake: 1,
            isSaveDisabled: true,
            isStartRecording: false,
            isRetakeDisabled: true,
            submitText: 'Submit',
            portrait:false,
            options: {
                controls: false,
                bigPlayButton: false,
                controlBar: {
                    deviceButton: false,
                    recordToggle: false,
                    pipToggle: false
                },
                width: 400,
                height: 300,
                fluid: true,
                plugins: {
                    record: {
                        pip: false,
                        audio: false,
                        video: true,
                        maxLength: 10,
                        debug: true
                    }
                }
            },
            ListToDo: [
                'to do 1 : move your left hand to up and down and say hello verification',
                'to do 2 : move your right hand to up and left and say hello verification 2',
                'to do 3 : move your hands to up and left and right and down and front and say hello verification 3',                
                'to do 4 : move your left hand to up and down and say hello verification 4',
                'to do 5 : move your right hand to up and left and say hello verification 5',
                'to do 6 : move your hands to up and left and right and down and front and say hello verification 6',
            ],
            listDoit: [],
            lenghtVideo:0,
            }
        },
        mounted(){
            window.addEventListener(
                "orientationchange",
                this.handleOrientationChange
            );
        
            const orientation = window.screen.orientation.type
            console.log('portrait screen '+window.screen.orientation.type);
            if (orientation === "portrait-primary") {
                this.portrait = true;
                console.log('portrait screen '+this.portrait);
            } else if (orientation === "landscape-primary") {
                this.portrait = false;
                console.log('landscape screen '+this.portrait);
            }

            this.generateRandomIndex();
            this.setupPage();
            this.player = videojs('myVideo2', this.options);
            this.player.on('deviceReady', () => {                
                this.player.record().start();                
            });
            this.player.on('deviceError', () => {
                console.log('device error:', this.player.deviceErrorCode);
            });
            this.player.on('error', (element, error) => {
                console.error(error);
            });
            // user clicked the record button and started recording
            this.player.on('startRecord', () => { 
                this.countDown();   
            //    this.countDown();
            });
            this.player.on('finishRecord', () => {
                // this.isSaveDisabled = false;
                // this.isStartRecording = false;
                this.setupPage();
                this.saveVideo();
                console.log('finishRecord');
            });
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
            countDown(){
                this.lenghtVideo = this.options['plugins']['record']['maxLength'];
                var time = setInterval(() => {
                    if(this.lenghtVideo <= 0) {
                        this.lenghtVideo = 0;
                        clearInterval(time);
                    }else{                        
                        this.lenghtVideo --;
                    }
                }, 1000);
            },
            generateRandomIndex() {
                var nums = [];
                for (let i = 0; i < this.ListToDo.length; i++) {
                   nums.push(i);                    
                }
                var length = 3;
                var j = 0;
                while (length--) {
                    j = Math.floor(Math.random() * (nums.length));
                    this.listDoit.push(nums[j]);
                    nums.splice(j,1);
                }
            },
            startRecording() {
                this.isStartRecording = true;
                this.player.record().getDevice();
            },
            async setupPage() {
                var vm = this ;
                await vm.setupCamera();
                vm.video.play();                
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
            saveVideo(){
                var data = this.player.recordedData;
                let formData = new FormData();        
                formData.append('video', data);    
                formData.append('retake', this.retake);
                formData.append('idverification', this.$store.state.idVerification);
          
                this.player.record().stopDevice();

                this.$auth.post('/api/uploadVideo', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            }
                    }).then(result => {  
                        this.retake += 1;
                        if(this.retake <= 3){
                            this.randomNote ++;
                            this.startRecording();
                        }else {
                            this.$store.dispatch('success', true); 
                            console.log( this.$store.state.success );
                            this.$router.push('/success');
                        }
                    }).catch(err => {
                    }
                );
            },
            
        }
    }
</script>

<style scoped>
#myVideo {
    min-width: 700px !important;
    min-height: 600px !important;  
}

#video_box{
    position:relative;
    -webkit-box-align:center;
    -webkit-box-pack:center;
    box-align:center;
    box-pack:center;
    overflow: hidden;
}

.imgMask img {    
    max-width: 100%;
    max-height: 100%;
}

#video_overlays {
    position: absolute;
    width: 100%;
    height: 98.8%;
    z-index: 300000;
    text-align: center;
    top: 0%;
}

#canvasVideo {
    min-width: 700px !important;
    min-height: 600px !important;  
}

.inputVideo{
     -webkit-transform: scaleX(-1);
    transform: scaleX(-1);
    width: auto;
    height: auto;
}

.contentVideo {
  position: fixed;
  top: 50px;
  left: 0;
  width: 100%;
  padding: 20px;  
}

.contentVideoRecord{
  top: 9rem;
  width: 65%; 
}

.imgMask {
    width: 800px;
    height: 600px;    
}
/* Add some content at the bottom of the video/page */
.contentBtn {
  color: #f1f1f1;
  width: 100%;
  padding: 20px;align-content: flex-end;

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
  font-size: 24px;
  color: #2C3E50;
  margin: 0.5em 0;
  width: 50%;
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
    margin-top: 2em;
}

.subBtn {
    background-color: #2FC877;
    border-color: #2FC877;
    border-radius: 5rem;
    width: 7em;
}


@media (max-width: 980px) {
    h2 {
        font-size: 18px;
    }
}

@media (max-width: 768px) {
    h2 {
        font-size: 13px;
    }
}



</style>