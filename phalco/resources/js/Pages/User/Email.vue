<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {useQuasar} from "quasar";

const $q = useQuasar()

const form = useForm({
    email: null,
})

const onSubmit = () => {
    const url = route('email.update')

    form.submit('put', url, {
        onSuccess: () => {
            $q.notify({
                message: 'Emailを変更しました',
            });
        },
    });
}
</script>
<template>
    <Head title="メールアドレス変更" />
    <MenuLayout>
        <div class="row">
            <h6>メールアドレスを変更する</h6>
        </div>
        <q-card>
            <q-form @submit="onSubmit">
                <span>新しいメールアドレスを入力</span>
                <q-input 
                    v-model="form.email" 
                    outlined 
                    filled 
                    dense 
                    :rules="[val => !!val || '必須項目です']"
                    :error="!!form.errors.email"
                    :error-message="form.errors.email"
                >
                </q-input>
                <div class="row q-mt-md q-gutter-x-md">
                    <q-btn 
                        flat 
                        label="キャンセル" 
                        @click="router.get(route('profile.show_my_profile'))"
                    />
                    <q-btn
                        type="submit"
                        label="変更"
                        color="primary"
                    />
                </div>
            </q-form>
        </q-card>
    </MenuLayout>
</template>

