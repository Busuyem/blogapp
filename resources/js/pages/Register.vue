<template>
  <div class="auth-card">
    <h2>Register</h2>
    <div v-if="error" class="error">{{ error }}</div>
    <form @submit.prevent="submitRegister">
      <input v-model="name" type="text" placeholder="Name" required />
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Password" required />
      <input v-model="password_confirmation" type="password" placeholder="Confirm Password" required />
      <button type="submit">Register</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const error = ref(null);
const router = useRouter();

async function submitRegister() {
  error.value = null;
  try {
    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/register`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })
  });

  const data = await res.json();

  if (!res.ok) {
    const message = data.message ||
      (data.errors ? Object.values(data.errors).flat().join(', ') : 'Registration failed');
    throw new Error(message);
  }

  if (data.token) {
    localStorage.setItem('token', data.token);
  }

  // redirect
  router.push('/');

  } catch (err) {
    error.value = err.message;
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
