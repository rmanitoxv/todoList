<template>
    <div class="flex justify-between w-1/3 items-center">
        <label
            for="is_done"
            class="cursor-pointer"
            @click="
                () => {
                    todo.is_done = !todo.is_done;
                    if (todo.todo_group_id) handleCheckbox();
                }
            "
        >
            <v-icon v-if="todo.is_done" name="md-checkbox-round" />
            <v-icon v-else name="md-checkboxoutlineblank" />
        </label>
        <input
            type="text"
            class="!outline-none bg-transparent border-b border-white text-center text-xl"
            v-model="todo.name"
            :disabled="!isOnEdit"
        />
        <button
            type="button"
            :class="`border-none p-0 hover:text-red-500 bg-transparent hover:bg-transparent ${
                !isOnEdit && 'opacity-0'
            }`"
            :onclick="
                () => {
                    handleRemoveTask(index, todo.id);
                }
            "
        >
            <v-icon name="bi-x-lg" />
        </button>
    </div>
</template>
<script>
export default {
    props: {
        todo: Object,
        index: Number,
        isOnEdit: Boolean,
        handleRemoveTask: Function
    },
    methods: {
        handleCheckbox() {
            axios
                .patch("/api/todo/" + this.$props.todo.id, {
                    is_done: this.$props.todo.is_done,
                })
                .then((response) => console.log(response))
                .catch((error) => console.error(error));
        },
        handleDelete() {
            if (this.$props.todo.todo_group_id) {
                console.log(this.$props.todo);
            }
        },
    },
};
</script>
