import api from './api'

export const tutorService = {
  list(params = {}) {
    return api.get('/tutors', { params })
  },

  get(id) {
    return api.get(`/tutors/${id}`)
  },

  create(payload) {
    return api.post('/tutors', payload)
  },

  update(id, payload) {
    return api.put(`/tutors/${id}`, payload)
  },

  remove(id) {
    return api.delete(`/tutors/${id}`)
  }
}