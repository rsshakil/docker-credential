<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {ROLE, ROLE_OPTIONS} from "@/Config/Role.js";

const $q = useQuasar()

const props = defineProps({
    administrator: Object,
})

const form = useForm({
    name: props.administrator ? props.administrator.name : '',
    email: props.administrator ? props.administrator.email : '',
    password: '',
    role: props.administrator ? props.administrator.role : ROLE.OP,
})

const onSubmit = () => {
    let url, method
    if (props.administrator) {
        url = route('admin.administrators.update', {administrator: props.administrator.id})
        method = 'put'
    } else {
        url = route('admin.administrators.store')
        method = 'post'
    }

    form.submit(method, url, {
        preserveScroll: true,
        onSuccess: () => {
            $q.notify({
                message: '保存しました',
            })
        },
        onError: (error) => {
            $q.notify({
                type: 'negative',
                message: Object.values(error).join(', '),
                html: true,
            })
        }
    })
}
</script>

<template>
    <Head title="アドミンユーザー編集" />

    <AuthenticatedLayout>
        <template #header>
            アドミンユーザー編集
        </template>

        <q-form @submit="onSubmit">
            <q-input
                filled
                v-model="form.name"
                label="名前"
                :error="!!form.errors.name"
                :error-message="form.errors.name"
                class="q-mt-md"
            />

            <q-input
                filled
                v-model="form.email"
                label="メールアドレス"
                :error="!!form.errors.email"
                :error-message="form.errors.email"
                class="q-mt-md"
            />

            <q-input
                filled
                v-model="form.password"
                label="パスワード"
                :error="!!form.errors.password"
                :error-message="form.errors.password"
                class="q-mt-md"
            />

            <div class="q-mt-md">権限</div>
            <q-option-group
                v-model="form.role"
                :options="ROLE_OPTIONS"
                inline
                class="q-mt-sm"
            />

            <div class="row q-mt-md q-gutter-x-md">
                <q-btn
                    type="submit"
                    label="保存"
                    color="primary"
                />
                <q-btn
                    type="button"
                    label="キャンセル"
                    @click="router.get(route('admin.administrators.index'))"
                />
            </div>
        </q-form>
    </AuthenticatedLayout>
</template>
