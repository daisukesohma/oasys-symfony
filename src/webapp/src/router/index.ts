import Vue from 'vue'
import VueRouter from 'vue-router'
import {apolloClient} from "@/apollo";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";

Vue.use(VueRouter);

let cguGuard = (to, from, next) => {
    apolloClient.query({
        query: LOGGED_USER
    }).then((res) => {
        if (!res.data.me.id) {
            next({name: 'Login', params: {next: to}});
        } else if (!res.data.me.cguAccepted) {
            next({name: 'cgu'});
        } else {
            next();
        }
    }).catch(error => {
        next({name: 'Login', params: {next: to}});
    });
};

let rightGuard = (rights?: string[]) => (to, from, next) => {
    cguGuard(to, from, route => {
        if (typeof route !== 'undefined') {
            next(route);
        } else {
            // @ts-ignore
            let user = apolloClient.cache.readQuery({query: LOGGED_USER}).me;
            if (!user
                || ((typeof rights === 'undefined' || user.rights.filter(right => rights.includes(right)).length === 0) && !(user.type === 'admin'))) {
                next({name: 'home'});
            } else {
                next();
            }
        }
    });
};

let userTypeGuard = (types: string[]) => (to, from, next) => {
    cguGuard(to, from, route => {
        if (typeof route !== 'undefined') {
            next(route);
        } else {
            // @ts-ignore
            let user = apolloClient.cache.readQuery({query: LOGGED_USER}).me
            if (!user || !types.includes(user.type)) {
                next({name: 'home'});
            } else {
                next();
            }
        }
    });
};

let routes = [
    {
        path: '*',
        component: () => import('@/views/error/404.vue'),
    },
    {
        path: '/',
        name: 'home',
        component: () => import('@/views/Home.vue'),
    },
    {
        path: '/connexion',
        name: 'Login',
        component: () => import('@/views/non-authenticated/Login.vue'),
    },
    {
        path: '/inscription/:linkId',
        name: 'UserInvite',
        component: () => import('@/views/non-authenticated/userInvite.vue'),
    },
    {
        path: '/accept-cgu',
        name: 'cgu',
        component: () => import('@/views/non-authenticated/AcceptCgu.vue'),
    },
    {
        path: '/mot-de-passe-oublie',
        name: 'ForgetPassword',
        component: () => import('@/views/non-authenticated/ForgetPassword.vue'),
    },
    {
        path: '/update-password',
        name: 'UpdatePassword',
        component: () => import('@/views/non-authenticated/UpdatePassword.vue'),
    },
    {
        path: '/email-validation/:token?',
        name: 'EmailValidation',
        component: () => import('@/views/non-authenticated/EmailValidation.vue'),
    },
    {
        path: '/expired-token',
        name:'ExpiredToken',
        component: () => import('@/views/non-authenticated/UpdatePasswordInvalidToken.vue'),
    },
    {
        path: '/admin-dashboard',
        name: 'AdminDashboard',
        component: () => import('@/views/AdminDashboard.vue'),
    },
    {
        path: '/page-introuvable',
        name: 'PageIntrouvable',
        component: () => import('@/components/utils/PageIntrouvable.vue'),
    },
    {
        path: '/about',
        name: 'about',
        component: () => import(/* webpackChunkName: "about" */ '@/views/About.vue'),
    },
    {
        path: '/users-list',
        name: 'Users',
        component: () => import('@/views/administration/user/usersList.vue'),
        beforeEnter: rightGuard(['ROLE_ACCESS_USER_MENU']),
    },
    {
        path: '/user/:id?',
        name: 'UsersForm',
        component: () => import('@/views/administration/user/userForm.vue'),
        beforeEnter: rightGuard(['ROLE_CREATE_USER', 'ROLE_UPDATE_USER']),
    },
    {
        path: '/user/:id/profile',
        name: 'ProfileUser',
        component: () => import('@/views/administration/user/profile.vue'),
        beforeEnter: userTypeGuard(['coach', 'admin']),
    },
    {
        path: '/users/import',
        name: 'ImportUsers',
        component: () => import('@/views/administration/user/importUsers.vue'),
        beforeEnter: rightGuard(['ROLE_CREATE_USER']),
    },
    {
        path: '/mon-compte',
        name: 'Me',
        component: () => import('@/views/me/me.vue'),
        beforeEnter: userTypeGuard(['admin', 'coach', 'candidate', 'support']),
    },
    {
        path: '/roles-list',
        name: 'RolesList',
        component: () => import('@/views/administration/role/rolesList.vue'),
        beforeEnter: userTypeGuard(['admin']),
    },
    {
        path: '/role/:id?',
        name: 'RolesForm',
        component: () => import('@/views/administration/role/rolesForm.vue'),
        beforeEnter: rightGuard(['ROLE_CREATE_ROLE', 'ROLE_UPDATE_ROLE']),
    },
    {
        path: '/events-list',
        name: 'EventsList',
        component: () => import('../views/administration/event/eventsList.vue'),
        beforeEnter: userTypeGuard(['admin', 'coach', 'support']),
    },
    {
        path: '/event/:id?/:mode?',
        name: 'EventForm',
        component: () => import('../views/administration/event/eventForm.vue'),
        beforeEnter: userTypeGuard(['admin', 'coach', 'support']),
    },
    {
        path: '/programs-list',
        name: 'ProgramsList',
        component: () => import('@/views/administration/program/programsList.vue'),
        beforeEnter: rightGuard(['ROLE_ACCESS_PROGRAM']),
    },
    {
        path: '/program/:id/event/:eid?/:document?',
        name: 'ProgramEventDocumentForm',
        component: () => import('@/views/administration/event/eventForm.vue'),
        beforeEnter: userTypeGuard(['admin', 'coach', 'support']),
    },
    {
        path: '/program/:id/event/:eid?/:mode?',
        name: 'ProgramEventForm',
        component: () => import('@/views/administration/event/eventForm.vue'),
        beforeEnter: userTypeGuard(['admin', 'coach', 'support']),
    },
    {
        path: '/programModel/:id/event/:eid?/:mode?',
        name: 'ProgramModelEventForm',
        component: () => import('@/views/administration/event/eventForm.vue'),
        beforeEnter: userTypeGuard(['admin', 'coach', 'support']),
    },
    {
        path: '/programModel/:id?',
        name: 'ProgramModelForm',
        component: () => import('@/views/administration/program/programForm.vue'),
        beforeEnter: rightGuard(['ROLE_CREATE_PROGRAM', 'ROLE_UPDATE_PROGRAM']),
    },
    {
        path: '/program/:id?/:mode?',
        name: 'ProgramForm',
        component: () => import('@/views/administration/program/programForm.vue'),
        beforeEnter: userTypeGuard(['admin', 'coach', 'support']),
    },
    {
        path: '/program/:programId?/import',
        name: 'ImportEvents',
        component: () => import('@/views/administration/event/importEvents.vue'),
        beforeEnter: rightGuard(['ROLE_CREATE_EVENT']),
    },
    {
        path: '/company-list',
        name: 'CompaniesList',
        component: () => import('@/views/administration/company/companyList.vue'),
        beforeEnter: rightGuard(['ROLE_ACCESS_COMPANY_MENU']),
    },
    {
        path: '/company/:id?',
        name: 'CompanyForm',
        component: () => import('@/views/administration/company/companyForm.vue'),
        beforeEnter: rightGuard(['ROLE_CREATE_COMPANY', 'ROLE_UPDATE_COMPANY']),
    },
    {
        path: '/documents-list',
        name: 'DocumentsList',
        component: () => import('@/views/document/documentsList.vue'),
    },
    {
        path: '/document/:id?/:mode?',
        name: 'DocumentForm',
        component: () => import('@/views/document/documentForm.vue'),
        beforeEnter: rightGuard(['ROLE_CREATE_DOCUMENT', 'ROLE_UPDATE_DOCUMENT']),
    },
    {
        path: '/document-sign/:member',
        name: 'DocumentSign',
        component: () => import('@/views/document/documentSign.vue'),
    },
    {
        path: '/event/:id?/document/:document?',
        name: 'EventDocumentForm',
        component: () => import('@/views/administration/event/eventForm.vue'),
    },

    {
        name: "CoachDashboard",
        path: "/coach-home",
        component: () => import('../views/coach/dashboard.vue'),
        beforeEnter: userTypeGuard(['coach']),
    },
    {
        name: "CoachCalendar",
        path: "/coach-calendar",
        component: () => import('../views/coach/calendar.vue'),
        beforeEnter: userTypeGuard(['coach']),
    },
    {
        name: "ViewCandidate",
        path: "/coach/candidate/:id/:program",
        component: () => import('../views/coach/candidatePage.vue'),
        beforeEnter: userTypeGuard(['coach']),
    },
    {
        name: "CandidateDashboard",
        path: "/candidate-dashboard",
        component: () => import('../views/candidate/dashboard.vue'),
        beforeEnter: userTypeGuard(['candidate']),
    },
    {
        name: "CandidateHome",
        path: "/candidate",
        component: () => import('../views/candidate/home.vue'),
        beforeEnter: userTypeGuard(['candidate']),
    },
    {
        name: "FaqList",
        path: "/questions-list",
        component: () => import('../views/administration/faq/faqList.vue'),
        beforeEnter: userTypeGuard(['admin', 'coach', 'support']),
    },
    {
        name: "QuestionForm",
        path: "/question-form/:id?",
        component: () => import('../views/administration/faq/QuestionForm.vue'),
        beforeEnter: userTypeGuard(['admin', 'coach', 'support']),
    },
    {
        name: "CandidateFaq",
        path: "/faq",
        component: () => import('../views/candidate/questionsDisplay.vue'),
        beforeEnter: userTypeGuard(['candidate']),
    },
    {
        name: "CandidateQuestion",
        path: "/question/:id?",
        component: () => import('../views/candidate/faqQuestion.vue'),
        beforeEnter: userTypeGuard(['candidate']),
    },
    {
        name: "CandidateContactUs",
        path: "/contact-us",
        component: () => import('../views/candidate/contactUs.vue'),
        beforeEnter: userTypeGuard(['candidate']),
    },
];

let router: VueRouter;
router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes: routes,
});

router.beforeEach((to, from, next) => {
    if(to.fullPath !== '/connexion' && to.fullPath !== '/mot-de-passe-oublie') {
        apolloClient.query({query: LOGGED_USER })
    }
    next();
});

export default router
