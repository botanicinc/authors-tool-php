<template>
    <!-- APP CONTENT -->
    <div>
        <div class="container">
            <span class="project-title">
                Projects
            </span>

            <button class="btn btn-botanic btn-sm float-right" v-on:click="showAddEditProjectModal">New Project</button>
            <div>&nbsp;</div>

            <div v-for="p in _.orderBy(projects, 'id', 'desc')" class="projectslist"> 

                <div class="project col"  v-bind:class="{ 'closed': !projectClosed[p.id] }">
                    <div class="arrow-handle" v-on:click="toggleProject(p.id)">
                        <img src="../assets/images/arrowup.svg" class="arrowup">
                        <img src="../assets/images/arrowdown.svg" class="arrowdown">
                    </div>
                    <div class="project-name">
                        {{ p.name }}
                        <div class="settings-icon" v-on:click="showAddEditProjectModal(p)"></div>
                    </div>
                    <div class="separator"></div>
                    <div class="full-width col conv-builder" >
                        <div class="conv-builder-manage row">
                            <label class="col-10">Conversation Builder </label>
                            <div class="col-2 text-right">
                                <button class="btn btn-edit-conv" v-on:click="goConvBuilder(p)">Edit</button>                                
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>

        <add-edit-project-modal ref="addEditProjectModal" />
        <confirm-modal ref="confirmDeleteModal" title="Delete Project" confirmButton="Delete"  />

    </div>
</template>

<script>
    /**
     * Project list
     * 
     */
    import { EventBus } from '../eventBus.js';
    import AddEditProjectModal from './AddEditProjectModal.vue';
    import ConfirmModal from './ConfirmModal.vue';
    import Vue from 'vue';

    export default {
        name: 'ProjectsList',
        components: {
            'add-edit-project-modal': AddEditProjectModal,
            'confirm-modal': ConfirmModal
        },
        data() {
            return {
                projectClosed: [],
                projects: []
            };
        },
        created() {
            EventBus.$on('showDeleteProjectModal', (args) => {                
                this.showDeleteProjectModal(args.projectId);
            });

            this.loadProjects();
        },
         beforeDestroy() {
            EventBus.$off('showDeleteProjectModal');            
        },

        mounted() {

        },
        methods: {
            /**
             * Loads projects from logged user's organization
             * 
             */
            loadProjects() {
                this.$http.get('/api/project')
                        .then(response => {
                            this.projects = response.data;
                        })
                        .catch(e => {
                            console.log(e);
                        });
            },
            
            /**
             * Leads the browser to the specified project
             * 
             * @param {Object} project Project object             
             */
            goConvBuilder(project) {
                this.$router.push({name: 'FlowBuilder', params: {projectId: project.id, projectName: project.name}});
            },

            /**
             * Launches project CRUD modal
             * 
             * @param {Object} project Project object
             */
            showAddEditProjectModal(project) {

                var id = 0;
                var addProjectPromise;
                if (project.id) {
                    id = project.id;
                    //load project config
                    addProjectPromise = this.$http.get(`/api/project/${id}`);
                } else {
                    //dummy promise auto resolvable
                    addProjectPromise = new Promise(function (resolve, reject) {
                        resolve();
                    });
                }

                //when config loaded, launch modal
                addProjectPromise.then((response) => {
                    var newProject = {};
                    if (response && response.data) {
                        newProject = response.data;
                    }
                    newProject.id = id;

                    delete(newProject.bot_id);

                    this.$refs.addEditProjectModal.show(newProject)
                            .then((newProject) => {
                                newProject.id = id;
                                project.name = newProject.name;
                                this.addProject(newProject);
                            });
                });
            },

            /**
             * Adds a project to the logged users' organization
             * 
             * @param {Object} newProject New project object
             */
            addProject(newProject) {

                if (!newProject.name) {
                    return;
                }

                this.$http.post(`/api/project/${newProject.id}`, newProject)
                        .then(response => {
                            if (!newProject.id) {
                                this.goConvBuilder(response.data);
                            }
                        });

            },

            /**
             * Launches project delete confirmation
             * 
             * @param {Int} projectId Project id
             */
            showDeleteProjectModal(projectId) {
                var text = {text: `Do you want to delete the project?`};

                this.$refs.confirmDeleteModal.confirm(text)
                        .then(() => {
                            this.deleteProject(projectId);
                        });
            },

            /**
             * Deletes a project form the logged user's organization
             * 
             * @param {Int} projectId Project id
             */
            deleteProject(projectId) {
                this.$http.delete('/api/project/' + projectId)
                        .then(response => {
                            this.loadProjects();
                        });
            },

            /**
             * toggles project iu section
             * 
             * @param {Int} projectId Project id
             */
            toggleProject(projectId) {
                Vue.set(this.projectClosed, projectId, !this.projectClosed[projectId]);
            }


        }


    }
</script>

