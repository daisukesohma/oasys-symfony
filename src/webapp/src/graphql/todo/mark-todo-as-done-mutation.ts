import gql from 'graphql-tag';

export const MARK_TODO_AS_DONE = gql`
    mutation markTodoAsDone (
        $todoId: String!,
    ) {
        markTodoAsDone (
            todoId: $todoId,
        ) {
            id,
            label,
            done,
        }
    }
`;