import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const user = ref(localStorage.getItem('user_id') ? {
    id: localStorage.getItem('user_id'),
    name: localStorage.getItem('user_name')
  } : null)

  
  const isLoggedIn = computed(() => !!token.value)

  function setAuth(newToken, newUser) {
    token.value = newToken
    user.value = newUser

    localStorage.setItem('token', newToken)
    if (newUser) {
      localStorage.setItem('user_id', newUser.id)
      localStorage.setItem('user_name', newUser.name)
    }
  }

  function clearAuth() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user_id')
    localStorage.removeItem('user_name')
  }

  return { token, user, isLoggedIn, setAuth, clearAuth }
})
