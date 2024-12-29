<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {useQuasar} from "quasar";
import AggregationComponent from "./AggregationComponent.vue";
import { KYC_STATUS_TEXT } from "@/Config/KycStatus";
import { LANGUAGE_OPTIONS, LANGUAGE_TEXT } from "@/Config/Language";
import { ref } from "vue";

const $q = useQuasar()

const props = defineProps({
    user: Object,
})

const columns = [
    {
        name: 'name',
        label: '名前',
        field: (row) => row.user.name,
        align: 'left',
        sortable: false,
    },
    {
        name: 'rate',
        label: '価格',
        align: 'left',
        sortable: false,
    },
    {
        name: 'amount',
        label: '商品残り|取引可能範囲',
        align: 'left',
        sortable: false,
    },
    {
        name: 'payment',
        label: '支払方法',
        align: 'left',
        sortable: false,
    },
    {
        name: 'action',
        label: '',
        align: 'left',
        sortable: false,
    },
]

const isLanguageEdit = ref(false);

const form = useForm({
    language: props.user.language,
})

const onLanguageEdit = () => {
    const url= route('profile.update')

    form.submit('put', url, {
        onSuccess: () => {
            $q.notify({
                message: '変更しました',
            })
            isLanguageEdit.value = false
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
    <Head title="アカウント情報" />
    <MenuLayout>
        <AggregationComponent
            :user="props.user"
            :sameUser="true"
        />
        <q-card>
            <div class="row">
                <h6 class="no-padding no-margin">アカウント情報</h6>
            </div>

            <q-list bordered>
                <q-item>
                    <q-item-section>パスワード</q-item-section>
                    <q-item-section>登録済</q-item-section>
                    <q-item-section>
                        <q-btn
                            label="変更"
                            @click="router.visit(route('password.edit'))"
                        />
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>メールアドレス</q-item-section>
                    <q-item-section>{{ user.email }}</q-item-section>
                    <q-item-section>
                        <q-btn
                            label="変更"
                            @click="router.visit(route('email.edit'))"
                        />
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>本人確認</q-item-section>
                    <q-item-section>{{ KYC_STATUS_TEXT[props.user.kyc_status] }}</q-item-section>
                    <q-item-section>
                        <q-btn
                        :disable="props.user.kyc_status != 1 ? true:false"
                        label="提出"
                        @click="router.visit(route('kyc_application.create'))"
                        />
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>言語設定</q-item-section>
                    <q-item-section>{{ LANGUAGE_TEXT[props.user.language] }}</q-item-section>
                    <q-item-section>
                        <q-btn
                            label="変更"
                            @click="isLanguageEdit = true"
                        />
                    </q-item-section>
                </q-item>
            </q-list>
            <br/>
            <div class="row">
                <h6 class="no-padding no-margin">トレード設定</h6>
            </div>
            <q-list bordered>
                <q-item>
                    <q-item-section>支払方法</q-item-section>
                    <q-item-section>{{ props.user.buyer_payments_count>0? '登録済':'未登録' }}</q-item-section>
                    <q-item-section>
                        <q-btn
                            :label="props.user.buyer_payments_count>0? '編集':'追加'"
                            @click="router.visit(route('buyer_payment.show'))"
                        />
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>支払先情報</q-item-section>
                    <q-item-section>{{ props.user.seller_payments_count>0? '登録済':'未登録' }}</q-item-section>
                    <q-item-section>
                        <q-btn
                            :label="props.user.seller_payments_count>0? '編集':'追加'"
                            @click="router.visit(route('seller_payment.show'))"
                        />
                    </q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>商品送付先管理</q-item-section>
                    <q-item-section>{{ props.user.user_product_accounts_count>0? '登録済':'未登録' }}</q-item-section>
                    <q-item-section>
                        <q-btn
                            :label="props.user.user_product_accounts_count>0? '編集':'追加'"
                            @click="router.visit(route('user_product_account.show'))"
                        />
                    </q-item-section>
                </q-item>
            </q-list>
        </q-card>

        <q-dialog  v-model="isLanguageEdit" persistent>
            <q-card style="min-width: 350px">
                <q-form @submit="onLanguageEdit">
                    <q-card-section>
                        <div class="text-h6">言語を変更</div>
                    </q-card-section>
                    <q-card-section class="q-pt-none">
                        <q-select 
                            filled 
                            v-model="form.language" 
                            :options="LANGUAGE_OPTIONS" 
                            map-options
                            emit-value
                            :rules="[val => !!val || '必須項目です']"
                            :error="!!form.errors.language"
                            :error-message="form.errors.language"
                        />
                    </q-card-section>
                    <q-card-actions align="right" class="text-primary">
                        <q-btn 
                            flat 
                            label="キャンセル" 
                            v-close-popup 
                        />
                        <q-btn
                            type="submit"
                            label="変更"
                            color="primary"
                        />
                    </q-card-actions>
                </q-form>
            </q-card>
        </q-dialog>
    </MenuLayout>
</template>

