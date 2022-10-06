import { createStore } from "vuex"
import user from './modules/user'
import roles from './modules/roles'

import division from './modules/divisions/division'

import program from './modules/programs/program'
import project from './modules/programs/project'

import event from './modules/events/event'

import workshop from './modules/workshops/workshop'
import annexe from './modules/workshops/annexe'
import annexf from './modules/workshops/annexf'
import annexone from './modules/workshops/annexone'
import common from './modules/workshops/common'

import annual from './modules/reports/annual'

import publication from "./modules/publications/publication"

import tag from './modules/tags/tag'

const store = createStore({
    modules: {
        user, roles,
        division,
        program, project,
        event,
        workshop, annexe, annexf, annexone, common,
        annual,
        publication,
        tag,
    }
})

export default store
