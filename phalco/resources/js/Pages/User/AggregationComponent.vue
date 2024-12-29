
<script setup>
import { useForm} from "@inertiajs/vue3";
import { ref } from "vue";
import {useQuasar} from "quasar";

const $q = useQuasar()

const props = defineProps({
    user: Object,
    sameUser: Boolean,
})

const isUserNameEdit = ref(false);
const isAggregationDetail = ref(false);

const form = useForm({
    name: props.user.name,
})

const onUserNameEdit = () => {
    const url= route('profile.update')

    form.submit('put', url, {
        onSuccess: () => {
            $q.notify({
                message: '変更しました',
            })
            isUserNameEdit.value = false
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
    <q-card>
        <div class="row items-center q-gutter-lg">
            <div>
                <h6 class="no-padding no-margin">{{ props.user.name }}</h6>
            </div>
            <q-btn
                v-if="sameUser"
                label="編集"
                @click="isUserNameEdit = true"
            />
            <q-btn
                v-if="sameUser"
                label="ブロックユーザー管理"
            />
            <q-btn
                v-else
                label="ブロック"
            />
        </div>
        <q-list bordered>
            <q-item>
                <q-item-section>
                    <q-card>
                        <q-card-section>
                            トレード精度
                        </q-card-section>
                        <q-separator/>
                        <q-card-section>
                            {{ props.user['recent_achievement'].trade_accuracy ? props.user['recent_achievement'].trade_accuracy : 'データなし' }}
                        </q-card-section>
                    </q-card>
                </q-item-section>
                <q-item-section>
                    <q-card>
                        <q-card-section>
                            30日以内の完了率
                        </q-card-section>
                        <q-separator/>
                        <q-card-section>
                            {{ props.user['recent_achievement'].traded_times ? props.user['recent_achievement'].traded_times : 'データなし' }}
                        </q-card-section>
                    </q-card>
                </q-item-section>
                <q-item-section>
                    <q-card>
                        <q-card-section>
                            平均リリース時間
                        </q-card-section>
                        <q-separator/>
                        <q-card-section>
                            {{ props.user['recent_achievement'].comfirmed_period ? props.user['recent_achievement'].comfirmed_period : 'データなし' }}
                        </q-card-section>
                    </q-card>
                </q-item-section>
                <q-item-section>
                    <q-card>
                        <q-card-section>
                            平均支払時間
                        </q-card-section>
                        <q-separator/>
                        <q-card-section>
                            {{ props.user['recent_achievement'].paid_period ? props.user['recent_achievement'].paid_period : 'データなし' }}
                        </q-card-section>
                    </q-card>
                </q-item-section>
                <q-item-section>
                    <q-btn
                        label="実績"
                        @click="isAggregationDetail = true"
                    />
                </q-item-section>
            </q-item>
        </q-list>
    </q-card>

    <q-dialog v-if="sameUser" v-model="isUserNameEdit" persistent>
        <q-card style="min-width: 350px">
            <q-form @submit="onUserNameEdit">
                <q-card-section>
                    <div class="text-h6">新しい名前を入力</div>
                </q-card-section>
                <q-card-section class="q-pt-none">
                    <q-input 
                        v-model="form.name" 
                        outlined 
                        filled 
                        dense 
                        :rules="[val => !!val || '必須項目です']"
                        :error="!!form.errors.name"
                        :error-message="form.errors.name"
                    >
                    </q-input>
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

    <q-dialog v-model="isAggregationDetail">
        <q-card style="min-width: 350px">
            <q-card-section>
                <div class="text-h6">実績詳細</div>
            </q-card-section>
            <q-card-section class="q-pt-none">
                <q-item>
                    <q-item-section>トレード精度</q-item-section>
                    <q-item-section>{{ props.user['recent_achievement'].trade_accuracy ? props.user['recent_achievement'].trade_accuracy : 'データなし' }}</q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>30日以内の完了率</q-item-section>
                    <q-item-section>{{ props.user['recent_achievement'].traded_times ? props.user['recent_achievement'].traded_times : 'データなし' }}</q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>平均リリース時間</q-item-section>
                    <q-item-section>{{ props.user['recent_achievement'].comfirmed_period ? props.user['recent_achievement'].comfirmed_period : 'データなし' }}</q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>平均支払時間</q-item-section>
                    <q-item-section>{{ props.user['recent_achievement'].paid_period ? props.user['recent_achievement'].paid_period : 'データなし' }}</q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>総トレード数</q-item-section>
                    <q-item-section>{{ props.user['recent_achievement'].trade_accuracy ? props.user['recent_achievement'].trade_accuracy : 'データなし' }}</q-item-section>
                </q-item>
                <q-item>
                    <q-item-section>完了済みのトレード数</q-item-section>
                    <q-item-section>{{ props.user['recent_achievement'].trade_accuracy ? props.user['recent_achievement'].trade_accuracy : 'データなし' }}</q-item-section>
                </q-item>
            </q-card-section>
        </q-card>
    </q-dialog>
</template>
