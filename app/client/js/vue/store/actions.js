import axios from "axios"
axios.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest'
}
const cancelTokenSource = axios.CancelToken.source()

export default {
  setLoading({ commit }, status) {
    commit("SET_ISLOADING", status)
  },

  setMobile({ commit }, status) {
    commit("SET_IS_MOBILE", status)
  },

  setError({ commit }, error) {
    commit("SET_ERROR", error)
  },

  setSiteData({ commit }, data) {
    commit("SET_SITE_DATA", data)
  },

  createClient({ commit }, data) {
    const endpoint = '/api/v/1/client'

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(`${endpoint}/createClient`, data, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  createProject({ commit }, data) {
    const endpoint = '/api/v/1/project'

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(endpoint, data, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  deleteProject({ commit }, id) {
    const endpoint = '/api/v/1/project'

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.delete(`${endpoint}/${id}`, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  setInvoicePaid({ commit }, data) {
    const endpoint = `/api/v/1/invoice`

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(`${endpoint}/paid`, data, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  DeleteInvoice({ commit }, data) {
    const endpoint = `/api/v/1/invoice`

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(`${endpoint}/delete`, data, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  SaveInvoice({ commit }, data) {
    const endpoint = `/api/v/1/invoice`

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(`${endpoint}/save`, data, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  createWorkflow({ commit }, data) {
    const endpoint = `/api/v/1/project/${data.get("project_id")}/createWorkflow`

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(endpoint, data, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  getFullstory({ commit }, data) {
    const endpoint = `api/v/1/userstory/${data.id}/getFullstory`
    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resolve).catch(reject)
    })
  },

  addHours({ commit }, data) {
    const endpoint = `/api/v/1/userstory/${data.id}`

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(`${endpoint}/addHours`, data.data, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  updateUserStory({ commit }, data) {
    const endpoint = `api/v/1/userstory/${data.id}`
    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(`${endpoint}/update`, data.data, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  rearrangeWorkflowList({ commit }, data) {
    const endpoint = `/api/v/1/workflow/${data.id}/rearrange`

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(endpoint, data.list, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  createWorkflowUserStory({ commit }, data) {
    const endpoint = `/api/v/1/workflow/${data.get("id")}/addUserStory`

    return new Promise((resolve, reject) => {
      axios.get(endpoint).then(resp => {
        axios.post(endpoint, data, {
          headers: resp.data
        }).then(resolve).catch(reject)
      }).catch(reject)
    })
  },

  searchClient({ commit }, data) {
    return new Promise((resolve, reject) => {
      axios.get(`/api/v/1/search-clients/${data}`, {
        cancelToken: cancelTokenSource.token
      }).then(resolve).catch(reject)
    })
  },

  getPageData({ commit }, path) {
    commit('SET_ERROR', null)
    commit('SET_SITE_DATA', null)

    return new Promise((resolve, reject) => {
      axios.get(path).then(resp => {
        commit('SET_SITE_DATA', resp.data)
        resolve(resp.data)
      }).catch(error => {
        commit('SET_ERROR', error)
        let code = 404

        if (error.response && error.response.status && error.response.data) {
            code = error.response.status
        }

        this.dispatch("getErrorPage", code)
        reject(code)
      })
    })
  },
  getErrorPage({ commit }, error_code) {
    axios.get(error_code).catch((error) => {
      if (error.response && error.response.data) {
        commit('SET_SITE_DATA', error.response.data)
      }
    })
  }
}
