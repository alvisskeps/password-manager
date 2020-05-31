<template>
    <div class="container">
        <add-password ref="add-password"></add-password>
        <edit-password ref="edit-password" :password-data="this.selectedPasswordData"></edit-password>
        <div class="row justify-content-center mb-5">
            <div class="col-md-12 col-lg-8">
                <button class="btn btn-success float-right" @click="showAddPassword">Add password</button>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-8">
                <div v-if="passwordsData.length">
                    <div v-for="(passwordData, index) in passwordsData" :key="index" class="card mb-3">
                        <div class="card-body">
                            <h5>Service name: <b>{{ passwordData.service }}</b></h5>
                            <div class="input-group mt-3">
                                <input v-model="passwordData.userName" type="text" class="form-control" :id="'username-' + index" disabled>
                                <span class="input-group-btn" style="margin-left: 49px">
                                    <button @click="copyToClipboard('username', index)" class="btn btn-primary ml-2">
                                        Copy
                                    </button>
                                </span>
                            </div>
                            <div class="input-group mt-3">
                                <input v-model="passwordData.password" type="password" class="form-control" :id="'password-' + index" disabled>
                                <span class="input-group-btn">
                                    <button @click="showPassword(index)" class="btn btn-secondary ml-2" type="button"><i class="fa fa-eye"></i></button>
                                </span>
                                <span class="input-group-btn">
                                    <button @click="copyToClipboard('password', index)" class="btn btn-primary ml-2">
                                        Copy
                                    </button>
                                </span>
                            </div>
                            <hr>
                            <div class="float-right mt-2">
                                <button @click="deletePassword(index)" type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
                                <button @click="showEditPassword(index)" type="submit" class="btn btn-warning">Edit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="card">
                    <div class="card-body">
                        <h3>Currently no passwords are saved</h3>
                        <hr>
                        <h5>Press 'Add password' to add new password</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import addPassword from './add-password';
    import editPassword from './edit-password';

    export default {
        components: {
            'add-password': addPassword,
            'edit-password': editPassword
        },
        data: function() {
            return {
                passwordsData: [],
                selectedPasswordData: {}
            }
        },
        props: {
            passwords: {
                type: Array,
                default: []
            }
        },
        mounted() {
            this.passwordsData = this.passwords;
        },
        methods: {
            showAddPassword() {
                const element = this.$refs['add-password'].$el
                $(element).modal('show')
            },
            showEditPassword(index) {
                this.selectedPasswordData = this.passwords[index];

                const element = this.$refs['edit-password'].$el
                $(element).modal('show')
            },
            showPassword(index) {
                const password = document.getElementById('password-' + index);
                if (password.getAttribute('type') === 'password') {
                    password.setAttribute('type', 'text');
                } else {
                    password.setAttribute('type', 'password');
                }
            },
            copyToClipboard(fieldName, index) {
                const element = document.getElementById(fieldName + '-' + index);

                if (!element.value) {
                    return;
                }

                element.disabled = false
                element.select();
                document.execCommand('copy');
                element.disabled = true
                alert(fieldName + ' copied to clipboard');
            },
            deletePassword(index) {
                const result = confirm('Are you sure?');
                if (result) {
                    axios.post('/delete', {
                        id: this.passwords[index].id
                    }).then((response) => {
                        if (!response.data.success) {
                            alert('Something wrong');
                        }

                        this.passwordsData.splice(index, 1);
                    })
                }
            }
        }
    }
</script>
