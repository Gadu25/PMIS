import { createStore } from "vuex"
import user from './modules/user'
import division from './modules/divisions/division'
import program from './modules/programs/program'
import workshop from './modules/workshops/workshop'
const store = createStore({
    modules: {
        user,
        division,
        program,
        workshop
    }
})

export default store
