<template>
    <div
        :class="`fixed z-20 top-[10%] left-1/2 -translate-x-1/2 rounded-xl bg-slate-900 p-6 transition-all overflow-hidden select-none ${
            isNewListOpen ? 'w-[35rem] h-[36rem]' : 'w-0 h-0'
        }`"
    >
        <form
            @submit.prevent="handleSubmit"
            ref="formRef"
            class="flex flex-col justify-center items-center gap-4 h-full"
        >
            <h1 className="font-bold text-xl">NEW TODO LIST</h1>
            <input
                type="text"
                class="text-2xl text-center bg-transparent border-b border-white outline-none"
                placeholder="Title"
                v-model="title"
                required
                maxlength="16"
            />
            <div class="flex gap-4">
                <label for="deadline"> Deadline: </label>
                <input
                    type="date"
                    id="deadline"
                    v-model="deadline"
                    class="bg-transparent outline-none border-b border-white"
                    required
                    :min="new Date().toLocaleDateString('en-CA')"
                />
            </div>
            <div
                class="overflow-y-auto flex-grow flex flex-col items-center gap-4 w-full px-12"
            >
                <div
                    v-for="(i, index) in todoList"
                    class="flex w-full justify-between text-lg"
                >
                    <div class="flex gap-4">
                        <label for="name">Name</label>
                        <input
                            type="text"
                            id="name"
                            v-model="todoList[index]"
                            class="bg-slate-950 text-white outline-none"
                            required
                            maxlength="64"
                        />
                    </div>
                    <button
                        type="button"
                        class="border-none p-0 hover:text-red-500 hover:bg-transparent"
                        :onclick="() => todoList.splice(index, 1)"
                    >
                        <v-icon name="bi-x-lg" />
                    </button>
                </div>
                <div class="w-full flex justify-end">
                    <button
                        type="button"
                        class="py-1 px-2 text-xs flex gap-2 h-full items-center"
                        :onclick="
                            () => {
                                todoList.push('');
                            }
                        "
                    >
                        Add new task <v-icon name="co-plus" />
                    </button>
                </div>
            </div>
            <p class="text-red-500 text-lg">{{ error }}</p>
            <div
                class="border-t border-slate-500 flex w-full justify-between py-2"
            >
                <button type="button" :onclick="handleClose">CANCEL</button>
                <button>ADD</button>
            </div>
        </form>
    </div>
</template>
<script>
export default {
    name: "NewList",
    props: {
        isNewListOpen: Boolean,
        changeNewList: Function,
        getTodoLists: Function,
    },
    data() {
        return {
            todoList: ["", ""],
            deadline: "",
            title: "",
            error: "",
        };
    },
    methods: {
        handleClose() {
            this.$refs.formRef.reset();
            this.todoList = ["", ""];
            this.error = "";
            this.changeNewList(false);
        },
        handleSubmit() {
            this.error = "";
            if (!this.todoList.length) {
                return (this.error = "At least one task is required.");
            }
            axios
                .post("/api/todoGroup", {
                    title: this.title,
                    deadline: this.deadline,
                    todo: this.todoList,
                })
                .then(() => {
                    this.$props.getTodoLists();
                    this.$props.changeNewList(false);
                })
                .catch((error) => console.error(error));
        },
    },
};
</script>
