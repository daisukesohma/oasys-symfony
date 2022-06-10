import gql from 'graphql-tag';

export const CREATE_TODO_ITEM = gql`
    mutation createTodoItem (
        $label: String!,
        $programId: String!,
        $userId: String
    ) {
        createTodoItem (
            label: $label,
            programId: $programId,
            userId: $userId,
        ) {
            id,
            label,
            done,
        }
    }
`;