<template>
  <div>
    <h2>All Posts</h2>
    <ul>
      <li v-for="post in posts" :key="post.id">
        <router-link :to="`/posts/${post.id}`">{{ post.title }}</router-link>
        <p>{{ post.content.substring(0, 100) }}...</p>
        <small>by {{ post.user.name }}</small>
      </li>
    </ul>
    <p v-if="loading">Loading posts...</p>
    <p v-if="error" style="color:red">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const posts = ref([]);
const loading = ref(false);
const error = ref(null);

onMounted(async () => {
  loading.value = true;
  error.value = null;

  try {
    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/posts`, {
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
      }
    });

    if (!res.ok) throw new Error('Failed to fetch posts');

    posts.value = await res.json();
  } catch (err) {
    error.value = err.message;
  } finally {
    loading.value = false;
  }
});
</script>

