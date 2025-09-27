<template>
  <div class="container">
    <h1>Simple Blog</h1>
    <nav>
      <router-link to="/">Home</router-link>
      <template v-if="!isLoggedIn">
        | <router-link to="/login">Login</router-link>
        | <router-link to="/register">Register</router-link>
      </template>
      <template v-else>
        | <router-link to="/new-post">Create New Post</router-link>
        | <a href="#" @click.prevent="logout">Logout</a>
      </template>
    </nav>
    <router-view />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const isLoggedIn = ref(false);
const router = useRouter();

onMounted(() => {
  // check if token exists in localStorage
  isLoggedIn.value = !!localStorage.getItem('token');
});

function logout() {
  localStorage.removeItem('token');
  isLoggedIn.value = false;
  router.push('/login');
}
</script>

<style scoped>
nav {
  margin-bottom: 20px;
}
</style>
