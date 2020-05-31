<template>
    <div class="modal fade" tabindex="-1" role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Edit username or password</h3>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success d-none" id="edit-success-message">
                        Password saved successfully
                    </div>
                    <form class="mt-3">
                        <div class="alert alert-danger d-none" id="edit-error-message">
                            <ul style="margin-bottom: 0">
                                <li v-for="error in errors">{{ error }}</li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <h5>Edit password for: {{ editedPasswordData.service }}</h5>
                        </div>
                        <div class="form-group">
                            <label for="edited-username">Username</label>
                            <input v-model="editedPasswordData.userName" type="email" class="form-control" id="edited-username" aria-describedby="emailHelp" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label for="edited-password">Password</label>
                            <div class="input-group">
                                <input v-model="editedPasswordData.password" type="password" class="form-control" id="edited-password" placeholder="Password">
                                <span class="input-group-btn">
                                    <button @click="showPassword" class="btn btn-secondary reveal" type="button"><i class="fa fa-eye"></i></button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" @click="editPassword">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "edit-password",
        data: function () {
            return {
                editedPasswordData: {},
                errors: []
            }
        },
        props: {
            passwordData: {
                type: Object,
                default: {}
            }
        },
        watch: {
            passwordData: function(value){
                this.editedPasswordData = {...value};
            }
        },
        methods: {
            editPassword() {
                this.errors = [];
                document.getElementById('edit-error-message').classList.add('d-none');

                this.validateInput();

                if (this.errors.length) {
                    document.getElementById('edit-error-message').classList.remove('d-none');

                    return;
                }

                axios.post('/edit', {
                    editedPasswordData: this.editedPasswordData
                }).then((response) => {
                    if (!response.data.success) {
                        alert('Something wrong');

                        return;
                    }

                    document.getElementById('edit-success-message').classList.remove('d-none');
                    setTimeout(() => {
                        location.reload();
                    }, 500)
                })
            },
            validateInput() {
                if (!this.editedPasswordData.userName) {
                    this.errors.push("User name is required.");
                }

                if (!this.editedPasswordData.password) {
                    this.errors.push("Password is required.");
                }

                if (this.editedPasswordData.password.length > 20) {
                    this.errors.push("Max password length is 20");
                }

                if (this.editedPasswordData.password.length < 8) {
                    this.errors.push("Min password length is 8");
                }
            },
            showPassword() {
                const password = document.getElementById('edited-password');
                if (password.getAttribute('type') === 'password') {
                    password.setAttribute('type', 'text');
                } else {
                    password.setAttribute('type', 'password');
                }
            },
        }
    }
</script>

<style scoped>

</style>
