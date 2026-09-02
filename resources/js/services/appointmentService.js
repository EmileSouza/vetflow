import api from './api'

export const appointmentService = {
  list(params = {}) {
    return api.get('/appointments', { params })
  },

  get(id) {
    return api.get(`/appointments/${id}`)
  },

  create(payload) {
    return api.post('/appointments', payload)
  },

  update(id, payload) {
    return api.put(`/appointments/${id}`, payload)
  },

  remove(id) {
    return api.delete(`/appointments/${id}`)
  }
}