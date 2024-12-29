<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {ref} from "vue";
import {useQuasar} from "quasar";

const $q = useQuasar()

const form = useForm({
    nowPassword: null,
    newPassword: null,
    newPassword_confirmation: null,
})

const onSubmit = () => {
    const url = route('password.update')

    form.submit('put', url, {
        onSuccess: () => {
            $q.notify({
                message: 'パスワードを変更しました',
            });
        },
        onError: (error) => {
            $q.notify({
                type: 'negative',
                message: Object.values(error).join(', '),
                html: true,
            })
        }
    });
}
const isNowPasswordHidden = ref(true);
const isNewPasswordHidden = ref(true);
const isNewPassword_confirmationHidden = ref(true);
</script>
<template>
    <Head title="パスワード変更" />
    <MenuLayout>
        <div class="row">
            <h6>パスワードを変更する</h6>
        </div>
        <q-card>
            <q-form @submit="onSubmit">
                <span>現在のパスワードを入力</span>
                <q-input 
                    v-model="form.nowPassword" 
                    :type="isNowPasswordHidden ? 'password' : 'text'"
                    outlined 
                    filled 
                    dense 
                    :rules="[val => !!val || '必須項目です']"
                    :error="!!form.errors.nowPassword"
                    :error-message="form.errors.nowPassword"
                >
                <template #append>
                    <q-icon
                        :name="isNowPasswordHidden ? 'visibility_off' : 'visibility'"
                        class="cursor-pointer"
                        @click="isNowPasswordHidden = !isNowPasswordHidden"
                    />
                </template>
                </q-input>
                <span>新しいパスワードを入力</span>
                <q-input 
                    v-model="form.newPassword" 
                    :type="isNewPasswordHidden ? 'password' : 'text'"
                    outlined 
                    filled 
                    dense 
                    :rules="[val => !!val || '必須項目です']"
                    :error="!!form.errors.newPassword"
                    :error-message="form.errors.newPassword"
                >
                <template #append>
                    <q-icon
                        :name="isNewPasswordHidden ? 'visibility_off' : 'visibility'"
                        class="cursor-pointer"
                        @click="isNewPasswordHidden = !isNewPasswordHidden"
                    />
                </template>
                </q-input>
                <span>新しいパスワードを確認</span>
                <q-input 
                    v-model="form.newPassword_confirmation" 
                    :type="isNewPassword_confirmationHidden ? 'password' : 'text'"
                    outlined 
                    filled 
                    dense 
                    :rules="[val => (val==form.newPassword) || 'パスワードが一致しません']"
                    :error="!!form.errors.newPassword_confirmation"
                    :error-message="form.errors.newPassword_confirmation"
                >
                <template #append>
                    <q-icon
                        :name="isNewPassword_confirmationHidden ? 'visibility_off' : 'visibility'"
                        class="cursor-pointer"
                        @click="isNewPassword_confirmationHidden = !isNewPassword_confirmationHidden"
                    />
                </template>
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

