<template>
    <div>
        <div class="container">
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20 col-md-4">
                  <div class="login_header">
                      <h1>Login to the dashboard</h1>
                  </div>
                  <div class="mt-2">
                        <Input type="email" v-model="data.email"  placeholder="Email"  />
                    </div>
                    <div class="mt-2">
                        <Input type="password" v-model="data.password"  placeholder="******"  />
                    </div>
                    <div class="mt-2 login_footer">
                        <Button type="primary" @click="login" :disabled="isLogging" :loading="isLogging">{{isLogging ? 'Loging...' : 'Login'}}</Button>
                    </div>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    data(){
        return {
            data : {
                email : '', 
                password: ''
            }, 
            isLogging: false, 
        }
    }, 

    methods : {
        async login(){
            if(this.data.email.trim()=='') return this.w('Email is required')
            if(this.data.password.trim()=='') return this.w('Password is required')
            if(this.data.password.length < 6) return this.w('Incorrect login details')
            this.isLogging = true
            const res = await this.callApi('post', 'app/admin_login', this.data)
            if(res.status===200){
                this.s(res.data.msg)
                window.location = '/'
            }else{
                if(res.status===401){
                    this.w(res.data.msg)
                }else if(res.status==422){
                    for(let i in res.data.errors){
                        this.e(res.data.errors[i][0])
                    }
                }
                else{
                    this.e()
                }
            }
            this.isLogging = false
        }
    }
}
</script>
<style scoped>
    ._1adminOverveiw_table_recent {
        margin: 0 auto;
        margin-top: 220px;
    }
    .login_footer{
        text-align: center;
    }
    .login_header{
        text-align: center;
        margin-bottom: 25px;
    }
</style>