<template>
  <div class="post-form-card">
    <h2>New Post</h2>

    <div v-if="error" class="error">{{ error }}</div>
    <div v-if="success" class="success">{{ success }}</div>

    <form @submit.prevent="submitNewPost">
      <input v-model="title" type="text" placeholder="Post Title" required :disabled="loading"/>
      <textarea v-model="content" placeholder="Post Content" rows="6" required :disabled="loading"></textarea>
      <button type="submit" :disabled="loading">
        {{ loading ?'Creating...' : 'Create Post' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const title = ref('');
const content = ref('');
const error = ref(null);
const success = ref(null);
const loading = ref(false);
const router = useRouter();

async function submitNewPost() {
  loading.value = true;
  error.value = null;
  success.value = null;

  try {
    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/posts`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({ title: title.value, content: content.value })
    });

    const data = await res.json();

    if (!res.ok) {
      const message = data.message || 
        (data.errors ? Object.values(data.errors).flat().join(', ') : 'Creation failed');
      throw new Error(message);
    }

    success.value = 'Post created successfully!';
    router.push(`/posts/${data.id}`);
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.post-form-card {
  max-width: 600px;
  margin: 40px auto;
  padding: 20px;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

input, textarea {
  width: 100%;
  padding: 8px;
  margin-bottom: 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

button {
  background-color: #3498db;
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 6px;
  cursor: pointer;
}

button:hover {
  background-color: #2980b9;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error { color: red; margin-bottom: 10px; }
.success { color: green; margin-bottom: 10px; }
</style>
