<template>
    <div class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Add password</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 col-xs-12">
                            <div class="alert alert-success d-none" id="success-message">
                                Password added successfully
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 col-xs-12">
                            <h5>Generate safe password</h5>
                            <div class="result-container">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="result" disabled/>
                                    <span class="input-group-btn">
                                        <button @click="copyToClipboard" class="btn btn-primary ml-2" id="clipboard">
                                            Copy
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="settings">
                                <div class="setting">
                                    <label>Password length</label>
                                    <input v-model="passwordLength" class="form-control" type="number" id="length" min='8' max='20'/>
                                </div>
                                <div class="setting">
                                    <label>Include uppercase letters</label>
                                    <input v-model="passwordSettings.upperCase" type="checkbox" id="uppercase"/>
                                </div>
                                <div class="setting">
                                    <label>Include lowercase letters</label>
                                    <input v-model="passwordSettings.lowerCase" type="checkbox" id="lowercase"/>
                                </div>
                                <div class="setting">
                                    <label>Include numbers</label>
                                    <input v-model="passwordSettings.numbers" type="checkbox" id="numbers"/>
                                </div>
                                <div class="setting">
                                    <label>Include symbols</label>
                                    <input v-model="passwordSettings.symbols" type="checkbox" id="symbols" />
                                </div>
                            </div>
                            <button @click="generatePassword" class="btn btn-large btn-success" id="generate">
                                Generate password
                            </button>
                        </div>
                    </div>
                    <form class="mt-3">
                        <div class="alert alert-danger d-none" id="error-message">
                            <ul style="margin-bottom: 0">
                                <li v-for="error in errors">{{ error }}</li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <label for="service">Service name</label>
                            <input v-model="passwordData.service" type="email" class="form-control" id="service" aria-describedby="emailHelp" placeholder="Service">
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input v-model="passwordData.userName" type="email" class="form-control" id="username" aria-describedby="emailHelp" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label for="current-password">Password</label>
                            <div class="input-group">
                                <input v-model="passwordData.password" type="password" class="form-control" id="current-password" placeholder="Password">
                                <span class="input-group-btn">
                                    <button @click="showPassword" class="btn btn-secondary reveal" type="button"><i class="fa fa-eye"></i></button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" @click="addNewPassword">Add</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "add-password",

        data: function () {
            return {
                passwordData: {
                    service: '',
                    userName: '',
                    password: ''
                },
                passwordFunctions: {
                    lowerCase: this.getRandomLower,
                    upperCase: this.getRandomUpper,
                    numbers: this.getRandomNumber,
                    symbols: this.getRandomSymbol
                },
                resultElement: '',
                passwordLength: 20,
                passwordSettings: {
                    upperCase: true,
                    lowerCase: true,
                    numbers: true,
                    symbols: true
                },
                errors: []
            }
        },
        watch: {
            'passwordLength': {
                handler: function(val) {
                    if (val > 20) {
                        this.passwordLength = 20;
                    }
                    if (val < 8) {
                        this.passwordLength = 8;
                    }
                },
                deep: true,
            },
        },
        methods: {
            addNewPassword() {
                this.errors = [];
                document.getElementById('error-message').classList.add('d-none');

                this.validateInput();

                if (this.errors.length) {
                    document.getElementById('error-message').classList.remove('d-none');

                    return;
                }

                axios.post('/create', {
                    passwordData: this.passwordData
                }).then((response) => {
                    if (!response.data.success) {
                        alert('Something wrong');

                        return;
                    }

                    document.getElementById('success-message').classList.remove('d-none');
                    setTimeout(() => {
                        location.reload();
                    }, 500)
                })
            },
            getRandomLower() {
                return String.fromCharCode(Math.floor(Math.random() * 26) + 97);
            },
            getRandomUpper() {
                return String.fromCharCode(Math.floor(Math.random() * 26) + 65);
            },
            getRandomNumber() {
                return String.fromCharCode(Math.floor(Math.random() * 10) + 48);
            },
            getRandomSymbol() {
                const symbols = '!@#$%^&*(){}[]=<>/,.';
                return symbols[Math.floor(Math.random() * symbols.length)];
            },
            generatePassword() {
                let generatedPassword = '';
                const checkedTypeCount = this.passwordSettings.lowerCase
                    + this.passwordSettings.upperCase
                    + this.passwordSettings.numbers
                    + this.passwordSettings.symbols;

                if (!checkedTypeCount) {
                    return '';
                }

                for (let i = 0; i < this.passwordLength; i+= checkedTypeCount) {
                    Object.keys(this.passwordSettings).forEach(type => {
                        if (this.passwordSettings[type]) {
                            generatedPassword += this.passwordFunctions[type]();
                        }
                    });
                }

                document.getElementById('result').value = generatedPassword.slice(0, this.passwordLength);
            },
            validateInput() {
                if (!this.passwordData.userName) {
                    this.errors.push("User name is required.");
                }

                if (!this.passwordData.service) {
                    this.errors.push("Service name is required.");
                }

                if (!this.passwordData.password) {
                    this.errors.push("Password is required.");
                }

                if (this.passwordData.password.length > 20) {
                    this.errors.push("Max password length is 20");
                }

                if (this.passwordData.password.length < 8) {
                    this.errors.push("Min password length is 8");
                }
            },
            copyToClipboard() {
                const password = document.getElementById('result');

                if (!password.value) {
                    return;
                }

                password.disabled = false
                password.select();
                document.execCommand('copy');
                password.disabled = true
                alert('Password copied to clipboard');
            },
            showPassword() {
                const password = document.getElementById('current-password');
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
    .setting {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 15px 0;
    }

    #length {
        max-width: 60px;
    }
</style>
