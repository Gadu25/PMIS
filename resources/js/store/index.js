import { createStore } from "vuex"
import user from './modules/user'
import division from './modules/divisions/division'
import program from './modules/programs/program'
import project from './modules/programs/project'
import workshop from './modules/workshops/workshop'
const store = createStore({
    modules: {
        user,
        division,
        program,
        project,
        workshop
    }
})

export default store
