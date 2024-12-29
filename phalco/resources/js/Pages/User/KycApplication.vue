<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {useQuasar} from "quasar";

const $q = useQuasar()

const form = useForm({
    kycImage: null,
    kycPhoto: null,
})

const onSubmit = () => {
    const url = route('kyc_application.store')
    form.submit('post', url, {
        onSuccess: () => {
            $q.notify({
                message: '本人確認書類を提出しました',
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
</script>
<template>
    <Head title="本人確認" />
    <MenuLayout>
        <div class="row">
            <h6>書類を提出して本人確認を行う</h6>
        </div>
        <q-card>
            <q-form @submit="onSubmit">
                <h6 class="no-padding no-margin">本人確認書類を撮影した写真をアップロードする</h6>
                <span>
                    下記の中から１点提出してください。
                    ・運転免許証（表裏）
                    ・マイナンバーカード（表面のみ ※裏面はアップロードしないでください。）
                    ・在留カード（表裏）
                </span>
                <q-file
                    v-model="form.kycImage" 
                    filled
                    clearable
                    class="q-mt-md"
                    label="＋アップロードする"
                    :rules="[val => !!val || '必須項目です']"
                    :error="!!form.errors.kycImage"
                    :error-message="form.errors.kycImage"
                ></q-file>
                <span>
                    本人の顔写真を撮影してアップロードする
                </span>
                <q-file
                    v-model="form.kycPhoto" 
                    label="＋アップロードする"
                    filled
                    clearable
                    class="q-mt-md"
                    :rules="[val => !!val || '必須項目です']"
                    :error="!!form.errors.kycPhoto"
                    :error-message="form.errors.kycPhoto"
                ></q-file>
                <div class="row q-mt-md q-gutter-x-md">
                    <q-btn 
                        flat 
                        label="キャンセル" 
                        @click="router.get(route('profile.show_my_profile'))"
                    />
                    <q-btn
                        type="submit"
                        label="提出"
                        color="primary"
                    />
                </div>
            </q-form>
        </q-card>
    </MenuLayout>
</template>

