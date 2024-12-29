<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {KYC_STATUS, KYC_STATUS_TEXT} from "@/Config/KycStatus.js";
import {computed, ref} from "vue";
import KycRejectDialog from "@/Dialogs/Admin/KycApplication/KycRejectDialog.vue";

const $q = useQuasar()

const props = defineProps({
    user: Object,
    kycApplication: Object,
    rejectedReason: String,
})

const imageTab = ref(1);

const form = useForm({
    full_name: props.user.full_name,
})

// 編集できる
const canEdit = computed(() => [
    KYC_STATUS.REVIEWING,
].includes(props.user.kyc_status))

// 承認できる
const canApprove = computed(() => [
    KYC_STATUS.REVIEWING,
].includes(props.user.kyc_status))

// 却下できる
const canReject = computed(() => [
    KYC_STATUS.REVIEWING,
    KYC_STATUS.CONFIRMED,
].includes(props.user.kyc_status))

// 承認する
const onApprove = () => {
    $q.dialog({
        cancel: true,
        message: '承認しますか？',
    }).onOk(() => {
        form.post(route('admin.kyc_applications.approve', props.user.id), {
            onSuccess: () => {
                $q.notify({
                    message: '本人確認を承認しました',
                })
            },
        });
    })
}

// 却下する
const rejectForm = useForm({
    rejected_reason: '',
})
const onReject = () => {
    $q.dialog({
        component: KycRejectDialog,
        componentProps: {rejectForm}
    }).onOk(() => {
        rejectForm.post(route('admin.kyc_applications.reject', props.user.id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                $q.notify({
                    message: '本人確認を却下しました',
                })
            },
            onError: (error) => {
                $q.notify({
                    type: 'negative',
                    message: '却下理由を確認してください',
                })
            }
        })
    })
}

const deleteImage = (kycImageId) => {
    $q.dialog({
        cancel: true,
        message: '写真を削除しますか？',
    }).onOk(() => {
        router.delete(route('admin.kyc_images.delete', kycImageId), null, {
            onSuccess: () => {
                $q.notify({
                    message: '写真を削除しました',
                })
            },
        })
    })
}
</script>

<template>
    <Head title="KYC対応" />

    <AuthenticatedLayout>
        <template #header>
            KYC対応
        </template>

        <q-banner
            class="bg-light-blue text-white"
            :class="props.user.kyc_status === KYC_STATUS.CONFIRMED ? 'bg-green' : 'blue'"
        >
            {{ KYC_STATUS_TEXT[props.user.kyc_status] }}
        </q-banner>

        <q-list bordered separator class="q-mt-md">
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
        </q-list>

        <q-list bordered separator class="q-mt-md" v-if="!!props.rejectedReason">
            <q-item>
                <q-item-section class="label">却下理由</q-item-section>
                <q-item-section>{{ props.rejectedReason }}</q-item-section>
            </q-item>
        </q-list>

        <q-form @submit="onApprove" v-if="canReject">
            <q-card class="q-mt-md">
                <q-card-section class="row q-col-gutter-x-md">
                    <div class="col-6">
                        <q-tabs
                            v-model="imageTab"
                            dense
                            class="text-grey"
                            active-color="primary"
                            indicator-color="primary"
                            align="justify"
                        >
                            <q-tab
                                v-for="(image, index) in props.kycApplication.kyc_images"
                                :key="image.id"
                                :name="index + 1"
                                :label="index + 1"
                            />
                        </q-tabs>
                        <q-separator />
                        <q-tab-panels v-model="imageTab" animated>
                            <q-tab-panel
                                v-for="(image, index) in props.kycApplication.kyc_images"
                                :key="image.id"
                                :name="index + 1"
                            >
                                <div class="row justify-end">
                                    <q-btn
                                        label="この写真を削除する"
                                        color="red"
                                        @click="deleteImage(image.id)"
                                    />
                                </div>

                                <q-img
                                    :src="route('admin.kyc_images.get_image', image.id)"
                                    class="full-width q-mt-md"
                                />
                            </q-tab-panel>
                        </q-tab-panels>
                    </div>
                    <div class="col-6">
                        <q-input
                            filled
                            v-model="form.full_name"
                            label="フルネームを入力する"
                            :readonly="!canEdit"
                            :error="!!form.errors.full_name"
                            :error-message="form.errors.full_name"
                        />

                        <div class="row q-gutter-md q-mt-md">
                            <q-btn
                                v-if="canApprove"
                                type="submit"
                                label="承認"
                                color="green"
                            />
                            <q-btn
                                v-if="canReject"
                                type="button"
                                label="却下"
                                color="red"
                                @click="onReject"
                            />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </q-form>

        <div class="row q-mt-md q-gutter-x-md">
            <q-btn
                type="button"
                label="戻る"
                @click="router.get(route('admin.users.show', props.user.id))"
            />
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.label {
    max-width: 15rem;
}
</style>
