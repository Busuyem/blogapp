<template>
  <div class="auth-card">
    <h2>Login</h2>
    <div v-if="error" class="error">{{ error }}</div>
    <form @submit.prevent="submitLogin">
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')
const error = ref(null)
const router = useRouter()
const auth = useAuthStore()

async function submitLogin() {
  error.value = null
  try {
    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/login`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ 
        email: email.value, 
        password: password.value 
      })
    })

    const data = await res.json()

    if (!res.ok) {
      throw new Error(data.message || 'Login failed')
    }

    // Save auth data via store (reactive)
    if (data.user && data.token) {
      auth.setAuth(data.user, data.token)
    }

    // Redirect to home
    router.push('/')
  } catch (err) {
    error.value = err.message
  }
}
</script>

<style scoped>
.auth-card {
  max-width: 400px;
  margin: 40px auto;
  padding: 20px;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
input {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
}
button {
  width: 100%;
  padding: 8px;
  background-color: #3498db;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
button:hover {
  background-color: #2980b9;
}
.error {
  color: red;
  margin-bottom: 10px;
}
</style>
