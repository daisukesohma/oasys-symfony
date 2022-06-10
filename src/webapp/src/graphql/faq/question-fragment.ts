import gql from 'graphql-tag'

export const QUESTION_FRAGMENT = gql`
    fragment QuestionFragment on Question {
        id,
        question,
        response,
        theme,
        updatedAt,
        createdAt,
        createdBy {
            firstName
            lastName
        },
        program {
            id,
            name,
        },
    }
`;
