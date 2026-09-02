import api from './api'

export const medicalRecordService = {
  list(params = {}) {
    return api.get('/medical-records', { params })
  },

  get(id) {
    return api.get(`/medical-records/${id}`)
  },

  create(payload) {
    return api.post('/medical-records', payload)
  },

  update(id, payload) {
    return api.put(`/medical-records/${id}`, payload)
  }
}