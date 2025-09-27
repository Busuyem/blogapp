<template>
  <div class="post-detail">
    <div v-if="loading" class="loading">Loading post...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else>
      <div class="post-card">
        <h2 class="post-title">{{ post.title }}</h2>
        <p class="post-content">{{ post.content }}</p>
        <small class="post-author" v-if="post.user">by {{ post.user.name }}</small>

        <!-- Edit/Delete buttons for post owner -->
        <div v-if="isPostOwner" class="post-actions">
          <router-link :to="`/posts/${post.id}/edit`" class="btn edit">Edit</router-link>
          <button class="btn delete" @click="deletePost">Delete</button>
        </div>
      </div>

      <hr />

      <div class="comments-section">
        <h3>Comments ({{ post.comments.length }})</h3>
        <ul>
          <li v-for="comment in post.comments" :key="comment.id" class="comment-card">
            <p class="comment-text">{{ comment.comment }}</p>
            <small class="comment-author">by {{ comment.user?.name || 'Guest' }}</small>

            <div class="comment-actions">
              <!-- Only comment owner can see Edit (removed per requirement) -->
              <!-- Delete visible to comment owner or post owner -->
              <button
                v-if="canDeleteComment(comment)"
                class="btn delete"
                @click="deleteComment(comment.id)"
              >
                Delete
              </button>
            </div>
          </li>
        </ul>

        <!-- Add new comment -->
        <div v-if="isAuthenticated" class="add-comment">
          <h4>Add a Comment</h4>
          <textarea v-model="newComment" placeholder="Write a comment..." rows="3"></textarea>
          <button @click="submitComment">Submit</button>
        </div>
        <div v-else class="login-note">
          <small>Log in to add a comment (guests can also comment if API allows).</small>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const API = (import.meta.env.VITE_API_BASE_URL || '').replace(/\/$/, '');

const post = ref({
  id: null,
  title: '',
  content: '',
  user: { id: null, name: '' },
  comments: []
});
const loading = ref(true);
const error = ref(null);

const newComment = ref('');
const isAuthenticated = ref(!!localStorage.getItem('token'));

// normalize current user values
const currentUserId = Number(localStorage.getItem('user_id') || 0);
const currentUserName = localStorage.getItem('user_name') || '';

const isPostOwner = ref(false);

onMounted(fetchPost);

async function fetchPost() {
  loading.value = true;
  error.value = null;
  try {
    const res = await fetch(`${API}/posts/${route.params.id}`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token') || ''}`
      }
    });
    if (!res.ok) throw new Error(`Failed to fetch post (status ${res.status})`);
    const data = await res.json();

    // normalize post.user if returned
    if (data.user) {
      data.user = { id: Number(data.user.id), name: data.user.name };
    }

    // normalize comments so each comment has a user {id, name}
    data.comments = (data.comments || []).map(c => ({
      ...c,
      user: c.user
        ? { id: Number(c.user.id), name: c.user.name }
        : { id: Number(c.user_id || 0), name: c.user_id == currentUserId ? currentUserName || 'You' : 'Guest' }
    }));

    post.value = data;
    isPostOwner.value = Number(data.user?.id) === currentUserId;
  } catch (err) {
    error.value = err.message;
    console.error(err);
  } finally {
    loading.value = false;
  }
}

async function submitComment() {
  if (!newComment.value || !newComment.value.trim()) return alert('Comment cannot be empty');

  try {
    const res = await fetch(`${API}/posts/${post.value.id}/comments`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({ comment: newComment.value })
    });

    if (!res.ok) {
      let msg = `Failed to submit comment (status ${res.status})`;
      try {
        const d = await res.json();
        msg = d.message || (d.errors ? Object.values(d.errors).flat().join(', ') : msg);
      } catch {}
      throw new Error(msg);
    }

    const comment = await res.json();


    const normalized = {
      ...comment,
      user: comment.user ? { id: Number(comment.user.id), name: comment.user.name } : { id: Number(comment.user_id || currentUserId), name: currentUserName || 'You' }
    };

    post.value.comments.push(normalized);
    newComment.value = '';
  } catch (err) {
    alert(err.message);
    console.error(err);
  }
}

function canDeleteComment(comment) {
  // comment owner OR post owner can delete
  return Number(comment.user?.id) === currentUserId || isPostOwner.value;
}

async function deleteComment(commentId) {
  if (!confirm('Are you sure you want to delete this comment?')) return;
  try {
    const res = await fetch(`${API}/comments/${commentId}`, {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    if (!res.ok) {
      let msg = `Failed to delete comment (status ${res.status})`;
      try { const d = await res.json(); msg = d.message || msg; } catch {}
      throw new Error(msg);
    }

    post.value.comments = post.value.comments.filter(c => c.id !== commentId);
  } catch (err) {
    alert(err.message);
    console.error(err);
  }
}

async function deletePost() {
  if (!confirm('Are you sure you want to delete this post?')) return;
  try {
    const res = await fetch(`${API}/posts/${post.value.id}`, {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    if (!res.ok) throw new Error(`Failed to delete post (status ${res.status})`);
    alert('Post deleted successfully!');
    router.push('/');
  } catch (err) {
    alert(err.message);
    console.error(err);
  }
}
</script>

<style scoped>
.post-detail { margin-top: 30px; }
.loading { font-size: 18px; color: #555; }
.error { color: red; font-weight: bold; }

.post-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-bottom: 20px; }
.post-title { font-size: 28px; color: #2c3e50; margin-bottom: 10px; }
.post-content { font-size: 16px; margin-bottom: 10px; }
.post-author { font-size: 14px; color: #555; }

.post-actions { margin-top: 10px; }
.btn { padding: 6px 12px; border-radius: 5px; cursor: pointer; text-decoration: none; color: #fff; font-size: 13px; border: none; }
.btn.edit { background: #3498db; }
.btn.delete { background: #e74c3c; }
.btn:hover { opacity: 0.9; }

.comments-section { margin-top: 20px; }
.comment-card { background: #f7f7f7; padding: 10px 15px; border-radius: 6px; margin-bottom: 10px; display: block; }
.comment-text { margin-bottom: 5px; }
.comment-author { font-size: 12px; color: #777; display: block; margin-bottom: 6px; }
.comment-actions { display: flex; gap: 8px; margin-top: 6px; }

.add-comment textarea { width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 8px; }
.add-comment button { background-color: #3498db; color: #fff; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
.add-comment button:hover { background-color: #2980b9; }

.login-note { margin-top: 8px; color: #666; font-size: 13px; }
</style>
