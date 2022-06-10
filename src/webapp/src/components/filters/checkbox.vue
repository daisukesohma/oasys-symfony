<template>
    <div class="form-group custom-form custom-check mr-2 pl-3">
        <input
            type="checkbox" class="form-check-input" :id="id"
            :value="value"
            :checked="value"
            @change="$event => $emit('input', $event.target.checked)" />
        <label class="form-check-label mb-0 font-weight-normal" :for="id">{{ label }}</label>
    </div>
</template>
<script>
    export default {
        name: "List",
        props: {
            id: {
                type: String,
                required: true,
            },
            value: {
                required: true,
            },
            label: {
                type: String,
                required: true,
            },
            attributes: {
                type: Object,
                required: false,
            },
        },
    };
</script>
