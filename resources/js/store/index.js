import { createStore } from "vuex"
import user from './modules/user'
import division from './modules/divisions/division'
import program from './modules/programs/program'
import project from './modules/programs/project'
import workshop from './modules/workshops/workshop'
import annexe from './modules/workshops/annexe'
import annexf from './modules/workshops/annexf'
import annexone from './modules/workshops/annexone'
import common from './modules/workshops/common'
import tag from './modules/tags/tag'
const store = createStore({
    modules: {
        user,
        division,
        program, project,
        workshop, annexe, annexf, annexone, common,
        tag
    }
})

export default store
