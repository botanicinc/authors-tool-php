import Vue from 'vue';
import Router from 'vue-router';
import Login from '@/components/Login';
import AdminLayout from '@/components/AdminLayout';
import ProjectsList from '@/components/ProjectsList';
import FlowBuilder from '@/components/FlowBuilder';
import QuickTips from '@/components/QuickTips';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/',
            name: 'Login',
            component: Login
        },
        {
            path: '/',
            component: AdminLayout,
            children: [
                {
                    path: 'projects',
                    name: 'ProjectsList',
                    component: ProjectsList
                },

                {
                    path: 'project/:projectId/flow/:flowId?/:projectName?',
                    name: 'FlowBuilder',
                    component: FlowBuilder
                },

                {
                    path: 'quickTips',
                    name: 'QuickTips',
                    component: QuickTips
                }
            ]
        }
    ]
}
);
