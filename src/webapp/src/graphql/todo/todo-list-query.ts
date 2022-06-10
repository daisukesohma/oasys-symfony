import gql from 'graphql-tag';

export const TODO_LIST = gql`
    query todoList (
        $programId: String!,
        $userId: String
    ) {
        todoList (
            programId: $programId,
            userId: $userId
        ) {
            id,
            label,
            done,
        }
    }
`;