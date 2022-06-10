import gql from 'graphql-tag'

export const QUESTION_FRAGMENT = gql`
    fragment QuestionFragment on Question {
        id,
        question,
        theme,
    }
`;
