<script setup>
import {Head, router, useForm} from "@inertiajs/vue3";
import MenuLayout from "@/Layouts/MenuLayout.vue";
import {ref} from "vue";
import {useQuasar} from "quasar";
import AggregationComponent from "../AggregationComponent.vue";

const $q = useQuasar()

const props = defineProps({
    user: Object,
    registeredPaymentMethods: Object,
    unregisteredPaymentMethods: Object,
})
const isAddUnregisteredPaymentMethod = ref(false)

const form = useForm({
    payment_method_id: null,
})

const onSubmit = () => {
    const url= route('buyer_payment.store')

    form.submit('post', url, {
        onSuccess: () => {
            $q.notify({
                message: '保存しました',
            })
            isAddUnregisteredPaymentMethod.value = false
            form.payment_method_id = null
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

const onDelete = (registeredPaymentMethod) => {
    $q.dialog({
        message: '支払方法を削除しますか?',
        ok: {
            label: '確認'
        },
        cancel: {
            label: 'キャンセル'
        },
        persistent: true
    }).onOk(() => {
        const url =  route('buyer_payment.delete', [registeredPaymentMethod.id])
        router.delete(url, {
            onSuccess: () => {
                $q.notify({
                    message: '支払方法を削除しました',
                });
            }
        })
    })
}

const onActive = (registeredPaymentMethod) => {
    $q.dialog({
        message: registeredPaymentMethod.is_active == 1?'無効にしますか?':'有効にしますか?',
        ok: {
            label: '確認'
        },
        cancel: {
            label: 'キャンセル'
        },
        persistent: true
    }).onOk(() => {
        const url =  route('buyer_payment.toggle_active', [registeredPaymentMethod.id])
        router.put(url, null, {
            onSuccess: () => {
                $q.notify({
                    message: registeredPaymentMethod.is_active == 1?'無効にしました':'有効にしました',
                });
            },
        })
    })
}
</script>

<template>
    <Head title="支払方法一覧" />

    <MenuLayout>
        <AggregationComponent
            :user="props.user"
            :sameUser="true"
        />

        <q-card>
            <div class="row">
                <h6 class="no-padding no-margin">支払方法</h6>
                <q-btn
                    label="追加"
                    @click="isAddUnregisteredPaymentMethod = true"
                />
            </div>
            <q-list class="row" bordered v-for="(registeredPaymentMethod, index) in props.registeredPaymentMethods" :key="index">
                <q-card 
                    class="col-12"
                    bordered 
                    :style="registeredPaymentMethod.is_active == 1? '':'background-color: #cbcbcb'"
                >
                    <div>
                        <h6 class="no-padding no-margin">{{ registeredPaymentMethod.payment_method.name }}</h6>
                    </div>
                    <q-btn
                        label="削除"
                        @click="onDelete(registeredPaymentMethod)"
                    />
                    <q-btn
                        align="right"
                        :label="registeredPaymentMethod.is_active == 1? '無効にする':'有効にする'"
                        @click="onActive(registeredPaymentMethod)"
                    />
                </q-card>
            </q-list>
        </q-card>

        <q-dialog v-model="isAddUnregisteredPaymentMethod" persistent>
            <q-card style="min-width: 350px">
                <q-form @submit="onSubmit">
                    <q-card-section>
                        <div class="text-h6">支払方法を選択</div>
                    </q-card-section>
                    <q-card-section class="q-pt-none">
                        <q-select 
                            filled 
                            v-model="form.payment_method_id" 
                            :options="props.unregisteredPaymentMethods" 
                            option-label="name" 
                            option-value="id"
                            map-options
                            emit-value
                            label="支払方法" 
                            :rules="[val => !!val || '必須項目です']"
                            :error="!!form.errors.payment_method_id"
                            :error-message="form.errors.payment_method_id"
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
                            label="追加"
                            color="primary"
                        />
                    </q-card-actions>
                </q-form>
            </q-card>
        </q-dialog>
    </MenuLayout>
</template>

