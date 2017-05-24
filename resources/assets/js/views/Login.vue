<template>
  <el-form label-position="left" label-width="0px" class="login-container" @keyup.native.enter="submit">
    <h3 class="title">Login</h3>
    <el-form-item prop="email" :error="errors.get('email')">
      <el-input type="text" v-model="loginForm.email" @change="errors.clear('email')" auto-complete="off" placeholder="Email" icon="message"></el-input>
    </el-form-item>
    <el-form-item prop="password" :error="errors.get('password')">
      <el-input type="password" v-model="loginForm.password" @change="errors.clear('password')" auto-complete="off" placeholder="Password" icon="fa fa-key"></el-input>
    </el-form-item>
    <el-checkbox v-model="checked" checked class="remember">Remember password</el-checkbox>
    <el-form-item style="width:100%;">
      <el-button type="primary" style="width:100%;" @click.native.prevent="submit" :loading="isSubmitting" icon="fa fa-sign-in">Login</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import {requestLogin}  from '../endpoints';
import Errors from '../common/js/Errors';

export default {
  data() {
    return {
      isSubmitting: false,
      loginForm: {
        email: '',
        password: ''
      },
      errors: new Errors(),
      checked: true
    };
  },
  methods: {
    submit() {
      this.isSubmitting = true;

      this.$auth.login({
        data: this.loginForm,
        rememberMe: this.checked,
        success() {
          this.isSubmitting = false;
          this.$router.push({ path: '/users' });
        },
        error(error) {
      		this.errors.record(error.response.data);
          this.isSubmitting = false;
        }
      });
    }
  }
}

</script>

<style lang="scss" scoped>
.login-container {
  /*box-shadow: 0 0px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);*/
  -webkit-border-radius: 5px;
  border-radius: 5px;
  -moz-border-radius: 5px;
  background-clip: padding-box;
  margin: 180px auto;
  width: 350px;
  padding: 35px 35px 15px 35px;
  background: #fff;
  border: 1px solid #eaeaea;
  box-shadow: 0 0 25px #cac6c6;
  .title {
    margin: 0px auto 40px auto;
    text-align: center;
    color: #505458;
  }
  .remember {
    margin: 0px 0px 35px 0px;
  }
}
</style>
