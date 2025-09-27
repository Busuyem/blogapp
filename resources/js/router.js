import { createRouter, createWebHistory } from 'vue-router';

// Import pages
import PostList from './pages/PostList.vue';
import PostDetail from './pages/PostDetail.vue';
import NewPost from './pages/NewPost.vue';
import EditPost from './pages/EditPost.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';


const routes = [
  { path: '/', component: PostList },
  { path: '/posts/:id', component: PostDetail, props: true },
  { path: '/new-post', component: NewPost, meta: { requiresAuth: true } },
  { path: '/posts/:id/edit', component: EditPost, meta: { requiresAuth: true } },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
