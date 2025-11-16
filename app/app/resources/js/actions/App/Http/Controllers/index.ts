import Auth from './Auth'
import HomeView from './HomeView'
import Settings from './Settings'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    HomeView: Object.assign(HomeView, HomeView),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers