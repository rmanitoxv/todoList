<template>
    <form
        class="flex flex-col rounded-xl border-b border-slate-800 last:border-0"
        @submit.prevent="handleSubmit"
    >
        <div
            class="flex justify-between w-full bg-slate-900 py-6 px-12 items-center group cursor-pointer"
            @click="isTodoOpen = !isTodoOpen"
        >
            <div class="flex flex-col">
                <input
                    type="text"
                    :class="`font-bold text-2xl bg-transparent border-b outline-none ${
                        isOnEdit ? 'border-white' : 'border-transparent'
                    }`"
                    v-model="todoGroup.title"
                    :disabled="!isOnEdit"
                />
                <input
                    type="date"
                    :class="`bg-transparent border-b outline-none ${
                        isOnEdit ? 'border-white' : 'border-transparent'
                    }`"
                    v-model="todoGroup.deadline"
                    :disabled="!isOnEdit"
                />
            </div>
            <v-icon
                v-if="isTodoOpen"
                name="md-arrowdropup-round"
                class="group-hover:text-slate-950"
                scale="2"
            />
            <v-icon
                v-else
                name="md-arrowdropdown-round"
                class="group-hover:text-slate-950"
                scale="2"
            />
        </div>
        <div
            :class="`flex flex-wrap bg-slate-800 gap-4 w-full justify-between transition-all px-24 ${
                isTodoOpen && 'py-8'
            }`"
            :style="isTodoOpen ? 'height:' + todoRows : 'height: 0'"
        >
            <Todo
                v-for="(todo, index) in todoGroup.todo"
                :todo="todo"
                :index="index"
                :isOnEdit="isOnEdit"
                :handleRemoveTask="handleRemoveTask"
            />
            <div class="w-1/3 flex justify-center items-center">
                <button
                    type="button"
                    :class="`py-2 px-4 text-xs flex gap-2 items-center bg-slate-900 ${
                        !isOnEdit && 'opacity-0'
                    }`"
                    :onclick="
                        () => {
                            todoGroup.todo.push({
                                todo_group_id: todoGroup.id,
                                name: '',
                                is_done: false,
                            });
                            todoRows = `${
                                (Math.floor(this.todoGroup.todo.length / 2) +
                                    1) *
                                5
                            }rem`;
                        }
                    "
                >
                    Add new task <v-icon name="co-plus" />
                </button>
            </div>
        </div>
        <div
            v-if="isOnEdit"
            class="flex justify-end w-full bg-slate-900 gap-4 px-12 py-4"
        >
            <button
                type="button"
                class="bg-red-500 hover:text-red-500"
                @click="
                    () => {
                        getTodoLists();
                    }
                "
            >
                Cancel
            </button>
            <button class="bg-green-500 hover:text-green-500">Save</button>
        </div>
        <div
            v-else
            class="flex justify-end w-full bg-slate-900 gap-4 px-12 py-4"
        >
            <button
                type="button"
                class="bg-red-500 hover:text-red-500"
                @click="handleDelete"
            >
                Delete
            </button>
            <button
                type="button"
                class="bg-green-500 hover:text-green-500"
                @click="() => (isOnEdit = true)"
            >
                Edit
            </button>
        </div>
    </form>
</template>
<script>
import Todo from "./Todo.vue";
export default {
    props: {
        todoGroup: Object,
        getTodoLists: Function,
    },
    components: {
        Todo,
    },
    data() {
        return {
            isOnEdit: false,
            isTodoOpen: false,
            todoRows: `${
                (Math.floor(this.todoGroup.todo.length / 2) + 1) * 5
            }rem`,
            deletedTasks: [],
        };
    },
    methods: {
        handleSubmit() {
            axios
                .patch("/api/todoGroup/" + this.$props.todoGroup.id, {
                    title: this.$props.todoGroup.title,
                    deadline: this.$props.todoGroup.deadline,
                    todo: this.$props.todoGroup.todo.filter((item) => {
                        return item.id;
                    }),
                })
                .then((response) => console.log(response))
                .catch((error) => console.error(error));
            for (const todo of this.$props.todoGroup.todo) {
                if (!todo.id && todo.name.length) {
                    return axios.post("/api/todo", {
                        todo: this.$props.todoGroup.todo.filter((item) => {
                            return !item.id && item.name.length;
                        }),
                    });
                }
            }
            if (this.deletedTasks.length) {
                console.log(this.deletedTasks);
                axios.post("/api/deleteTodo", {
                    ids: this.deletedTasks,
                });
            }
        },
        handleRemoveTask(index, id) {
            if (id) this.deletedTasks.push(id);
            this.$props.todoGroup.todo.splice(index, 1);
        },
        handleDelete() {
            axios
                .delete("/api/todoGroup/" + this.$props.todoGroup.id)
                .then(() => this.$props.getTodoLists())
                .catch((error) => console.error(error));
        },
    },
    watch: {
        "todoGroup.todo": {
            handler: function (newValue, oldValue) {
                this.todoRows = `${
                    (Math.floor(newValue.length / 2) + 1) * 5
                }rem`;
            },
            deep: true,
        },
    },
};
</script>
