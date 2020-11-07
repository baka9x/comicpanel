import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)
//admin project pages
import home from './components/pages/home'
import category from './admin/pages/category'
import adminusers from './admin/pages/adminusers'
import login from './admin/pages/login'
import role from './admin/pages/role'
import assignRole from './admin/pages/assignRole'
import createComic from './admin/pages/createComic'
import comics from './admin/pages/comics'
import chapters from './admin/pages/chapters'
import createChapter from './admin/pages/createChapter'

import notfound from './admin/pages/notfound'

const routes = [
	//projects routes
	{
		path: '/',
		component: home,	
		name: 'home',	
	},

	{
		path: '/category',
		component: category,	
		name: 'category',	
	},

	{
		path: '/adminusers',
		component: adminusers,	
		name: 'adminusers',	
	},

	{
		path: '/login',
		component: login,	
		name: 'login',	
	},

	{
		path: '/role',
		component: role,	
		name: 'role',	
	},

	{
		path: '/assignRole',
		component: assignRole,	
		name: 'assignRole',	
	},
	{
		path: '/createComic',
		component: createComic,	
		name: 'createComic',	
	},
	{
        path: '/comics',
        component: comics,
        name: 'comics'

    },
    // {
    //     path: '/editblog/:id',
    //     component: editblog,
    //     name: 'editblog'

    // },

    {
		path: '/chapters',
		component: chapters,
		name: 'chapters',	
	},

    {
        path: '/createChapter',
        component: createChapter,
        name: 'createChapter'

    },

    // {
    //     path: '/comics',
    //     component: comics,
    //     name: 'comics'

    // },
    
	{
        path: '*',
        component: notfound,
        name: 'notfound'

    },


]

export default new Router({ 
	mode: 'history',
	routes
})