<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {date, useQuasar} from "quasar";
import {USER_STATUS_TEXT} from "@/Config/UserStatus.js";
import {KYC_STATUS_TEXT} from "@/Config/KycStatus.js";

const $q = useQuasar()

const props = defineProps({
    user: Object,
})
</script>

<template>
    <Head title="ユーザー詳細" />

    <AuthenticatedLayout>
        <template #header>
            ユーザー詳細
        </template>

        <q-list bordered separator>
            <q-item>
                <q-item-section class="label">ユーザー番号</q-item-section>
                <q-item-section>{{ props.user.user_no }}</q-item-section>
            </q-item>
            <q-item>
                <q-item-section class="label">ニックネーム</q-item-section>
                <q-item-section>{{ props.user.name }}</q-item-section>
            </q-item>

            <q-item>
                <q-item-section class="label">メールアドレス</q-item-section>
                <q-item-section>{{ props.user.email }}</q-item-section>
            </q-item>

            <q-item>
                <q-item-section class="label">登録日時</q-item-section>
                <q-item-section>{{ date.formatDate(props.user.created_at, 'YYYY/MM/DD HH:mm') }}</q-item-section>
            </q-item>

            <q-item>
                <q-item-section class="label">ユーザーステータス</q-item-section>
                <q-item-section>{{ USER_STATUS_TEXT[props.user.user_status] }}</q-item-section>
            </q-item>

            <q-item>
                <q-item-section class="label">KYCステータス</q-item-section>
                <q-item-section>{{ KYC_STATUS_TEXT[props.user.kyc_status] }}</q-item-section>
            </q-item>
        </q-list>

        <q-btn
            label="KYC対応"
            @click="router.visit(route('admin.kyc_applications.show', props.user.id))"
            class="q-mt-md"
        />

        <div class="row q-mt-md q-gutter-x-md">
            <q-btn
                type="button"
                label="戻る"
                @click="router.get(route('admin.users.index'))"
            />
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.label {
    max-width: 15rem;
}
</style>
