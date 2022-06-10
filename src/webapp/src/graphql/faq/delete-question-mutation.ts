import gql from 'graphql-tag'

export const DELETE_QUESTION = gql`
    mutation deleteQuestion($id: String!) {
        deleteQuestion(id: $id) {
            id,
        }
    }
`;
