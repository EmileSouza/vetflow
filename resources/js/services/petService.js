import api from './api'

export const petService = {
  list(params = {}) {
    return api.get('/pets', { params })
  },

  get(id) {
    return api.get(`/pets/${id}`)
  },

  create(payload) {
    return api.post('/pets', payload)
  },

  update(id, payload) {
    return api.put(`/pets/${id}`, payload)
  },

  remove(id) {
    return api.delete(`/pets/${id}`)
  }
}