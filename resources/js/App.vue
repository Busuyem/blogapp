<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const auth = useAuthStore()
const router = useRouter()

function logout() {
  auth.clearAuth()
  router.push('/login')
}
</script>

<template>
  <div class="container">
    <h1>Simple Blog</h1>
    <nav>
      <router-link to="/">Home</router-link>
      <template v-if="!auth.isLoggedIn">
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
