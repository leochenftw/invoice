export default {
  SET_ISLOADING(state, status) {
    state.isLoading = status
  },
  SET_IS_MOBILE(state, isMobile) {
    state.isMobile = isMobile
  },
  SET_ERROR(state, error) {
    state.error = error
  },
  SET_SITE_DATA(state, site_data) {
    state.site_data = site_data
    const root = document.documentElement
    if (site_data) {
      document.title = site_data.title
      if (site_data.site_title) {
        state.site_title = site_data.site_title
      }

      if (site_data.site_slogan) {
        state.site_slogan = site_data.site_slogan
      }

      if (site_data.background) {
        root.style.setProperty('--bgimg', `url(${site_data.background})`)
      } else {
        root.style.removeProperty('--bgimg')
      }
    } else {
      root.style.removeProperty('--bgimg')
    }
  }
}
