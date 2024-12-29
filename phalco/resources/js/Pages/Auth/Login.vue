<script setup>
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {Head, useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";

const $q = useQuasar()

const form = useForm({
    email: '',
    password: '',
})

const submit = () => {
    form.post(route('login'))
}
</script>

<template>
    <Head title="ログイン"/>

    <MenuLayout>
        <q-card class="q-mx-auto login-box">
            <q-card-section>
                <q-form @submit.prevent="submit">
                    <h1 class="text-center q-py-lg text-h5">ログイン</h1>

                    <q-input
                        filled
                        label="メールアドレス"
                        v-model="form.email"
                        required
                        autofocus
                        :error="!!form.errors.email"
                        :error-message="form.errors.email"
                        class="q-mt-md"
                    />

                    <q-input
                        filled
                        label="パスワード"
                        v-model="form.password"
                        required
                        type="password"
                        :error="!!form.errors.password"
                        :error-message="form.errors.password"
                        class="q-mt-md"
                    />

                    <q-btn
                        label="ログイン"
                        color="primary"
                        :disabled="form.processing"
                        type="submit"
                        class="block q-mx-auto q-mt-md"
                    />


                </q-form>
            </q-card-section>
        </q-card>
    </MenuLayout>
</template>

<style scoped>
.login-box {
    max-width: 500px;
}
</style>
