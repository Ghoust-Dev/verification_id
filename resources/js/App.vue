<template>
  <div id="app">   
    <div class="container-fluid"> 
        <router-view/>          
    </div>
  </div>     
</template>

<script>
import Home from './components/Home.vue';

export default {
  name: 'App',
  components: {
      Home,
  },
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#nav {
  padding: 30px;
}

#nav a {
  font-weight: bold;
  color: #2c3e50;
}

#nav a.router-link-exact-active {
  color: #42b983;
}

body{
background: -webkit-linear-gradient(to right, #333399, #ff00cc);
background: linear-gradient(to right, #333399, #ff00cc);
background-repeat: no-repeat;
background-attachment: fixed;

}
</style>